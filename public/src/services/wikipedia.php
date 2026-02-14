<?php

declare(strict_types=1);

/**
 * @return array{ok:bool,data?:array<string,mixed>,error?:array{code:string,message:string,details?:array<string,mixed>}}
 */
function fetch_wikipedia_metadata(string $pageUrl, array $options = []): array
{
    $normalizedUrlResult = normalize_wikipedia_url($pageUrl, $options);
    if (!$normalizedUrlResult['ok']) {
        return $normalizedUrlResult;
    }

    $normalizedUrl = $normalizedUrlResult['url'];
    $host = (string) parse_url($normalizedUrl, PHP_URL_HOST);
    $title = (string) $normalizedUrlResult['title'];

    $summaryUrl = 'https://' . $host . '/api/rest_v1/page/summary/' . rawurlencode($title);
    $summaryResponse = http_json_get($summaryUrl);

    if ($summaryResponse['ok']) {
        $summaryData = $summaryResponse['json'];
        $keyFacts = fetch_wikipedia_key_facts($host, $title);

        return [
            'ok' => true,
            'data' => [
                'title' => (string) ($summaryData['title'] ?? $title),
                'summary' => (string) ($summaryData['extract'] ?? ''),
                'thumbnail' => $summaryData['thumbnail']['source'] ?? null,
                'canonicalUrl' => (string) ($summaryData['content_urls']['desktop']['page'] ?? $normalizedUrl),
                'licenseText' => get_wikipedia_license_text($host),
                'keyFacts' => $keyFacts,
                'lastFetchedAt' => gmdate('c'),
            ],
        ];
    }

    if ($summaryResponse['status'] === 404) {
        return [
            'ok' => false,
            'error' => [
                'code' => 'page_not_found',
                'message' => 'Wikipedia page not found.',
                'details' => ['url' => $normalizedUrl],
            ],
        ];
    }

    $fallback = fetch_wikipedia_metadata_via_mediawiki($host, $title, $normalizedUrl);
    if ($fallback['ok']) {
        return $fallback;
    }

    return [
        'ok' => false,
        'error' => [
            'code' => 'upstream_failure',
            'message' => 'Failed to fetch metadata from Wikipedia.',
            'details' => [
                'summary_status' => $summaryResponse['status'],
                'summary_error' => $summaryResponse['error'],
                'fallback_error' => $fallback['error'] ?? null,
            ],
        ],
    ];
}

/**
 * @return array{ok:bool,url?:string,title?:string,error?:array{code:string,message:string,details?:array<string,mixed>}}
 */
function normalize_wikipedia_url(string $rawUrl, array $options = []): array
{
    $allowLanguageSubdomains = $options['allow_language_subdomains'] ?? true;
    $extraAllowedHosts = $options['extra_allowed_hosts'] ?? [];

    $trimmed = trim($rawUrl);
    if ($trimmed === '') {
        return [
            'ok' => false,
            'error' => [
                'code' => 'invalid_url',
                'message' => 'URL is required.',
            ],
        ];
    }

    $parts = parse_url($trimmed);
    if ($parts === false || empty($parts['host'])) {
        return [
            'ok' => false,
            'error' => [
                'code' => 'invalid_url',
                'message' => 'Invalid URL format.',
            ],
        ];
    }

    $scheme = strtolower((string) ($parts['scheme'] ?? 'https'));
    if (!in_array($scheme, ['http', 'https'], true)) {
        return [
            'ok' => false,
            'error' => [
                'code' => 'invalid_url',
                'message' => 'Only HTTP/HTTPS Wikipedia URLs are allowed.',
            ],
        ];
    }

    $host = strtolower((string) $parts['host']);
    $isEnglishHost = $host === 'en.wikipedia.org';
    $isLanguageSubdomain = (bool) preg_match('/^[a-z0-9-]+\.wikipedia\.org$/', $host);
    $isExtraHost = in_array($host, $extraAllowedHosts, true);

    if (!($isEnglishHost || $isExtraHost || ($allowLanguageSubdomains && $isLanguageSubdomain))) {
        return [
            'ok' => false,
            'error' => [
                'code' => 'invalid_url',
                'message' => 'URL host is not an allowed Wikipedia domain.',
                'details' => ['host' => $host],
            ],
        ];
    }

    $path = (string) ($parts['path'] ?? '');
    $query = [];
    parse_str((string) ($parts['query'] ?? ''), $query);

    $title = '';
    if (strpos($path, '/wiki/') === 0) {
        $title = rawurldecode(substr($path, strlen('/wiki/')));
    } elseif (!empty($query['title']) && is_string($query['title'])) {
        $title = rawurldecode($query['title']);
    }

    $title = trim($title);
    if ($title === '') {
        return [
            'ok' => false,
            'error' => [
                'code' => 'invalid_url',
                'message' => 'Wikipedia page URL must include a page title.',
            ],
        ];
    }

    $title = str_replace(' ', '_', $title);
    $normalizedUrl = 'https://' . $host . '/wiki/' . rawurlencode($title);

    return [
        'ok' => true,
        'url' => $normalizedUrl,
        'title' => $title,
    ];
}

/**
 * @return array{ok:bool,data?:array<string,mixed>,error?:array{code:string,message:string,details?:array<string,mixed>}}
 */
function fetch_wikipedia_metadata_via_mediawiki(string $host, string $title, string $normalizedUrl): array
{
    $apiUrl = 'https://' . $host . '/w/api.php?action=query&prop=extracts|pageimages|info&inprop=url&pithumbsize=1200&exintro=1&explaintext=1&redirects=1&format=json&titles=' . rawurlencode($title);
    $response = http_json_get($apiUrl);

    if (!$response['ok']) {
        if ($response['status'] === 404) {
            return [
                'ok' => false,
                'error' => [
                    'code' => 'page_not_found',
                    'message' => 'Wikipedia page not found.',
                    'details' => ['url' => $normalizedUrl],
                ],
            ];
        }

        return [
            'ok' => false,
            'error' => [
                'code' => 'upstream_failure',
                'message' => 'MediaWiki API request failed.',
                'details' => ['status' => $response['status'], 'error' => $response['error']],
            ],
        ];
    }

    $pages = $response['json']['query']['pages'] ?? [];
    if (!is_array($pages) || $pages === []) {
        return [
            'ok' => false,
            'error' => [
                'code' => 'upstream_failure',
                'message' => 'Unexpected MediaWiki API payload.',
            ],
        ];
    }

    $page = array_values($pages)[0];
    if (($page['missing'] ?? false) !== false) {
        return [
            'ok' => false,
            'error' => [
                'code' => 'page_not_found',
                'message' => 'Wikipedia page not found.',
                'details' => ['url' => $normalizedUrl],
            ],
        ];
    }

    return [
        'ok' => true,
        'data' => [
            'title' => (string) ($page['title'] ?? $title),
            'summary' => (string) ($page['extract'] ?? ''),
            'thumbnail' => $page['thumbnail']['source'] ?? null,
            'canonicalUrl' => (string) ($page['fullurl'] ?? $normalizedUrl),
            'licenseText' => get_wikipedia_license_text($host),
            'keyFacts' => fetch_wikipedia_key_facts($host, $title),
            'lastFetchedAt' => gmdate('c'),
        ],
    ];
}

/**
 * @return array<int,array{label:string,value:string}>
 */
function fetch_wikipedia_key_facts(string $host, string $title): array
{
    $apiUrl = 'https://' . $host . '/w/api.php?action=parse&format=json&prop=text&section=0&redirects=1&page=' . rawurlencode($title);
    $response = http_json_get($apiUrl);
    if (!$response['ok']) {
        return [];
    }

    $html = (string) ($response['json']['parse']['text']['*'] ?? '');
    if ($html === '') {
        return [];
    }

    return extract_wikipedia_key_facts_from_html($html);
}

/**
 * @return array<int,array{label:string,value:string}>
 */
function extract_wikipedia_key_facts_from_html(string $html): array
{
    if (!preg_match('/<table[^>]*class="[^"]*infobox[^"]*"[^>]*>(.*?)<\/table>/is', $html, $tableMatch)) {
        return [];
    }

    $tableHtml = $tableMatch[1];
    if (!preg_match_all('/<tr[^>]*>(.*?)<\/tr>/is', $tableHtml, $rowMatches)) {
        return [];
    }

    $facts = [];
    $factFields = [
        'type', 'classification', 'shape', 'constellation', 'distance', 'radius',
        'diameter', 'size', 'dimensions', 'mass', 'age', 'apparent magnitude',
    ];

    foreach ($rowMatches[1] as $rowHtml) {
        if (!preg_match('/<th[^>]*>(.*?)<\/th>/is', $rowHtml, $labelMatch)) {
            continue;
        }

        if (!preg_match('/<td[^>]*>(.*?)<\/td>/is', $rowHtml, $valueMatch)) {
            continue;
        }

        $label = clean_wikipedia_html_text($labelMatch[1]);
        $value = clean_wikipedia_html_text($valueMatch[1]);
        if ($label === '' || $value === '') {
            continue;
        }

        $labelLower = strtolower($label);
        $isRelevant = false;
        foreach ($factFields as $field) {
            if (strpos($labelLower, $field) !== false) {
                $isRelevant = true;
                break;
            }
        }

        if (!$isRelevant) {
            continue;
        }

        $facts[] = [
            'label' => $label,
            'value' => $value,
        ];

        if (count($facts) >= 8) {
            break;
        }
    }

    return $facts;
}

function clean_wikipedia_html_text(string $rawHtml): string
{
    $withoutSup = preg_replace('/<sup[^>]*>.*?<\/sup>/is', '', $rawHtml);
    $noTags = strip_tags((string) $withoutSup);
    $decoded = html_entity_decode($noTags, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $singleSpaced = preg_replace('/\s+/u', ' ', $decoded);

    return trim((string) $singleSpaced);
}

function get_wikipedia_license_text(string $host): string
{
    $siteInfoUrl = 'https://' . $host . '/w/api.php?action=query&meta=siteinfo&siprop=rightsinfo&format=json';
    $response = http_json_get($siteInfoUrl);

    if ($response['ok']) {
        $rightsText = (string) ($response['json']['query']['rightsinfo']['text'] ?? '');
        if ($rightsText !== '') {
            return $rightsText;
        }
    }

    return 'Text is available under the Creative Commons Attribution-ShareAlike License; additional terms may apply.';
}

/**
 * @return array{ok:bool,status:int,error:?string,json:array<string,mixed>}
 */
function http_json_get(string $url): array
{
    $context = stream_context_create([
        'http' => [
            'method' => 'GET',
            'header' => "Accept: application/json\r\nUser-Agent: NightSkyAtlas/1.0 (metadata fetch)\r\n",
            'timeout' => 8,
            'ignore_errors' => true,
        ],
    ]);

    $body = @file_get_contents($url, false, $context);
    $status = 0;

    if (isset($http_response_header[0]) && preg_match('#\s(\d{3})\s#', $http_response_header[0], $matches)) {
        $status = (int) $matches[1];
    }

    if ($body === false) {
        return ['ok' => false, 'status' => $status, 'error' => 'Network request failed.', 'json' => []];
    }

    $decoded = json_decode($body, true);
    if (!is_array($decoded)) {
        return ['ok' => false, 'status' => $status, 'error' => 'Invalid JSON from upstream.', 'json' => []];
    }

    if ($status >= 200 && $status < 300) {
        return ['ok' => true, 'status' => $status, 'error' => null, 'json' => $decoded];
    }

    $message = '';
    if (!empty($decoded['detail']) && is_string($decoded['detail'])) {
        $message = $decoded['detail'];
    } elseif (!empty($decoded['error']['info']) && is_string($decoded['error']['info'])) {
        $message = $decoded['error']['info'];
    }

    return ['ok' => false, 'status' => $status, 'error' => $message !== '' ? $message : 'Upstream returned non-success status.', 'json' => $decoded];
}
