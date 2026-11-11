# Lab 7: Cloud & Deployment Guide

## Objective
Deploy your portfolio website to a cloud platform and document the deployment process including comparison of IaaS, PaaS, and SaaS.

---

## Cloud Service Models Comparison

### IaaS (Infrastructure as a Service)
- **Provides**: Virtual machines, storage, networking
- **User Manages**: Operating systems, applications, data
- **Examples**: AWS EC2, Google Compute Engine, Azure VMs
- **Use Case**: When you need full control over the server environment

### PaaS (Platform as a Service)
- **Provides**: Ready-to-use environment for application deployment
- **User Manages**: Applications and data only
- **Examples**: Heroku, Google App Engine, Netlify
- **Use Case**: When you want to focus on code, not infrastructure

### SaaS (Software as a Service)
- **Provides**: Ready-made applications accessible via internet
- **User Manages**: Nothing (just uses the software)
- **Examples**: Gmail, Dropbox, Salesforce
- **Use Case**: End-user applications

| Feature | IaaS | PaaS | SaaS |
|---------|------|------|------|
| Control | High | Medium | Low |
| Management | User manages OS + App | User manages App only | Provider manages all |
| Flexibility | Maximum | Moderate | Minimum |
| Setup Time | Long | Short | Immediate |
| Cost Model | Pay per resource | Pay per use | Subscription |

---

## Deployment Steps (Netlify - PaaS)

### Step 1: Create GitHub Account & Repository
1. Go to https://github.com
2. Sign up or log in
3. Click "+" → "New Repository"
4. Name: `web-engineering-portfolio`
5. Select "Public"
6. Click "Create Repository"

### Step 2: Upload Your Files
```bash
# Initialize git in your project folder
git init

# Add all files
git add .

# Commit
git commit -m "Initial portfolio commit"

# Add remote repository
git remote add origin https://github.com/YOUR_USERNAME/web-engineering-portfolio.git

# Push to GitHub
git push -u origin main
```

### Step 3: Deploy to Netlify
1. Go to https://www.netlify.com
2. Click "Log in" → Sign in with GitHub
3. Click "Add new site" → "Import an existing project"
4. Select GitHub as Git provider
5. Authorize Netlify to access your repositories
6. Select your `web-engineering-portfolio` repository
7. Configure:
   - Branch to deploy: `main`
   - Build command: (leave empty for static HTML)
   - Publish directory: `/` (root)
8. Click "Deploy site"

### Step 4: Your Site is Live!
- Netlify will provide a URL: `https://your-site-name.netlify.app`
- Every push to GitHub will automatically redeploy

---

## Alternative: Deploy to GitHub Pages (Free)

1. Go to repository Settings
2. Scroll to "Pages" section
3. Source: Deploy from a branch
4. Select "main" branch and "/ (root)" folder
5. Click Save
6. Your site will be at: `https://yourusername.github.io/web-engineering-portfolio`

---

## Files to Include in Repository

```
web-engineering-portfolio/
├── index.html          (Main portfolio page)
├── css/
│   └── style.css      (Stylesheet)
├── js/
│   └── main.js        (JavaScript)
├── images/
│   └── profile.jpg    (Your photo)
├── README.md          (This guide)
└── .gitignore
```

---

## Documentation Checklist

- [ ] Screenshot of GitHub repository
- [ ] Screenshot of Netlify dashboard
- [ ] Screenshot of live deployed website
- [ ] Comparison table of IaaS/PaaS/SaaS
- [ ] Deployment steps documented
- [ ] Live URL shared

---

## Submission
Submit the following:
1. Live website URL
2. GitHub repository link
3. Screenshots of deployment process
4. Written comparison of cloud service models
