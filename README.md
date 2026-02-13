# images

Website to show off astronomy images.

## Planning

See `WEBSITE_TASKS.md` for a full implementation plan covering:
- public thumbnail gallery
- secure admin upload backdoor
- equipment metadata capture
- visual wow-factor enhancements

## Deploying from GitHub

Use the following commands on your deployment machine.

### First-time setup (enable Git + get code)

```bash
# 1) Install Git if needed (Ubuntu/Debian)
sudo apt-get update
sudo apt-get install -y git

# 2) Verify Git is available
git --version

# 3) Clone the repository
git clone https://github.com/<your-org-or-user>/<your-repo>.git
cd <your-repo>
```

### If the project folder already exists but isn't a Git repo yet

```bash
cd /path/to/your/project
git init
git remote add origin https://github.com/<your-org-or-user>/<your-repo>.git
git fetch origin
git checkout -t origin/main
```

### Pull the latest code for updates

```bash
cd /path/to/your/project
git pull origin main
```

After pulling, run your normal build/restart steps for the web server or app process.
