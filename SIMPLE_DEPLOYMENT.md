# Simple ProgenPHP Hostinger Deployment

## 🎯 The Simple Way (Just 4 Steps)

Forget all the complex guides. Here's the straightforward approach:

### Step 1: Push to GitHub
```bash
git add .
git commit -m "Ready for deployment"
git push origin main
```

### Step 2: SSH into Hostinger
```bash
ssh -p 65002 YOUR_USERNAME@ssh.hostinger.com
```

### Step 3: Clone Your Code
```bash
# Go to your domain folder
cd domains/YOUR_DOMAIN.com

# Clone outside web folder (more secure)
git clone https://github.com/mymaestro/progenphp.git

# Link only the public folder to web
ln -sf ../progenphp/public public_html/progenphp
```

### Step 4: Set Basic Permissions
```bash
cd progenphp
chmod 755 public private
chmod 644 public/* private/config/*
```

**That's it!** Visit: `https://yourdomain.com/progenphp/`

---

## 🤔 Need Your Hostinger Details

To give you the exact commands, I just need:

1. **Your Hostinger username** (starts with 'u' like u123456789)
2. **Your domain name** (like mysite.com)

Then I can give you the copy-paste commands.

---

## 🚨 If SSH Doesn't Work

Some Hostinger plans don't have SSH. Alternative:

1. **Zip your files**: `zip -r progenphp.zip .`
2. **Upload via File Manager** in hPanel
3. **Extract in** `public_html/`

---

## ❓ Questions?

Just ask:
- "How do I find my Hostinger username?"
- "What if SSH doesn't work?"
- "How do I enable SSL?"

**Let's keep it simple!** 😊