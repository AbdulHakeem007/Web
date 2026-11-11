# Lab 11: Authentication, AI Integration & Deployment

## Objective
Add complete user authentication system, integrate an AI chatbot API, implement security measures, and deploy to a production cloud environment.

---

## Part 1: User Authentication System

### Step 1: Create Authentication App

```bash
python manage.py startapp accounts
```

### Step 2: Settings Configuration

Update `settings.py`:

```python
INSTALLED_APPS = [
    'django.contrib.admin',
    'django.contrib.auth',
    'django.contrib.contenttypes',
    'django.contrib.sessions',
    'django.contrib.messages',
    'django.contrib.staticfiles',
    'accounts',  # Add this
    'blog',
]

# Email backend for development
EMAIL_BACKEND = 'django.core.mail.backends.console.EmailBackend'

# Login redirect
LOGIN_REDIRECT_URL = '/'
LOGOUT_REDIRECT_URL = '/login/'
```

### Step 3: Authentication Views

`accounts/views.py`:

```python
from django.shortcuts import render, redirect
from django.contrib.auth.models import User
from django.contrib.auth import authenticate, login, logout
from django.contrib.auth.decorators import login_required
from django.contrib import messages

def home(request):
    return render(request, 'accounts/home.html')

def register(request):
    if request.method == 'POST':
        username = request.POST.get('username')
        email = request.POST.get('email')
        password = request.POST.get('password')
        password2 = request.POST.get('password2')

        # Validation
        if password != password2:
            messages.error(request, 'Passwords do not match!')
            return render(request, 'accounts/register.html')

        if User.objects.filter(username=username).exists():
            messages.error(request, 'Username already exists!')
            return render(request, 'accounts/register.html')

        if User.objects.filter(email=email).exists():
            messages.error(request, 'Email already registered!')
            return render(request, 'accounts/register.html')

        # Create user - Django automatically hashes password!
        user = User.objects.create_user(
            username=username,
            email=email,
            password=password
        )
        messages.success(request, 'Account created successfully! Please login.')
        return redirect('login')

    return render(request, 'accounts/register.html')

def user_login(request):
    if request.method == 'POST':
        username = request.POST.get('username')
        password = request.POST.get('password')

        user = authenticate(request, username=username, password=password)

        if user is not None:
            login(request, user)
            messages.success(request, f'Welcome back, {username}!')
            return redirect('home')
        else:
            messages.error(request, 'Invalid username or password!')

    return render(request, 'accounts/login.html')

@login_required
def user_logout(request):
    logout(request)
    messages.success(request, 'You have been logged out.')
    return redirect('login')
```

### Step 4: URL Configuration

`accounts/urls.py`:

```python
from django.urls import path
from . import views

urlpatterns = [
    path('', views.home, name='home'),
    path('register/', views.register, name='register'),
    path('login/', views.user_login, name='login'),
    path('logout/', views.user_logout, name='logout'),
]
```

Update main `urls.py`:

```python
from django.contrib import admin
from django.urls import path, include

urlpatterns = [
    path('admin/', admin.site.urls),
    path('', include('accounts.urls')),
    path('blog/', include('blog.urls')),
]
```

### Step 5: Templates

`templates/accounts/base.html`:

```html
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{% block title %}My Site{% endblock %}</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; max-width: 800px; margin: 0 auto; padding: 20px; background: #f5f5f5; }
        .container { background: white; padding: 30px; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.1); }
        nav { background: #2c3e50; padding: 15px; border-radius: 8px; margin-bottom: 20px; }
        nav a { color: white; text-decoration: none; margin-right: 20px; }
        nav a:hover { color: #3498db; }
        .messages { margin-bottom: 20px; }
        .message { padding: 12px; border-radius: 6px; margin-bottom: 10px; }
        .success { background: #d4edda; color: #155724; }
        .error { background: #f8d7da; color: #721c24; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: 600; }
        input { width: 100%; padding: 12px; border: 2px solid #ddd; border-radius: 6px; }
        button { padding: 12px 30px; background: #3498db; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 600; }
        button:hover { background: #2980b9; }
    </style>
</head>
<body>
    <div class="container">
        <nav>
            <a href="{% url 'home' %}">Home</a>
            {% if user.is_authenticated %}
                <span style="color: #3498db;">Welcome, {{ user.username }}!</span>
                <a href="{% url 'logout' %}">Logout</a>
            {% else %}
                <a href="{% url 'login' %}">Login</a>
                <a href="{% url 'register' %}">Register</a>
            {% endif %}
        </nav>

        {% if messages %}
        <div class="messages">
            {% for message in messages %}
            <div class="message {{ message.tags }}">{{ message }}</div>
            {% endfor %}
        </div>
        {% endif %}

        {% block content %}{% endblock %}
    </div>
</body>
</html>
```

`templates/accounts/register.html`:

```html
{% extends 'accounts/base.html' %}

{% block title %}Register{% endblock %}

{% block content %}
<h2>Register</h2>
<form method="POST">
    {% csrf_token %}
    <div class="form-group">
        <label>Username:</label>
        <input type="text" name="username" required>
    </div>
    <div class="form-group">
        <label>Email:</label>
        <input type="email" name="email" required>
    </div>
    <div class="form-group">
        <label>Password:</label>
        <input type="password" name="password" required>
    </div>
    <div class="form-group">
        <label>Confirm Password:</label>
        <input type="password" name="password2" required>
    </div>
    <button type="submit">Register</button>
</form>
<p>Already have an account? <a href="{% url 'login' %}">Login here</a></p>
{% endblock %}
```

`templates/accounts/login.html`:

```html
{% extends 'accounts/base.html' %}

{% block title %}Login{% endblock %}

{% block content %}
<h2>Login</h2>
<form method="POST">
    {% csrf_token %}
    <div class="form-group">
        <label>Username:</label>
        <input type="text" name="username" required>
    </div>
    <div class="form-group">
        <label>Password:</label>
        <input type="password" name="password" required>
    </div>
    <button type="submit">Login</button>
</form>
<p>Don't have an account? <a href="{% url 'register' %}">Register here</a></p>
{% endblock %}
```

`templates/accounts/home.html`:

```html
{% extends 'accounts/base.html' %}

{% block title %}Home{% endblock %}

{% block content %}
<h1>Welcome {{ request.user.username }}</h1>
<p>You are successfully logged in!</p>
<a href="{% url 'logout' %}">Logout</a>
{% endblock %}
```

---

## Part 2: AI Chatbot Integration

### Step 1: Install OpenAI

```bash
pip install openai
```

### Step 2: Settings

Add to `settings.py`:

```python
import os
from dotenv import load_dotenv

load_dotenv()

OPENAI_API_KEY = os.getenv('OPENAI_API_KEY', 'your-api-key-here')
```

### Step 3: Chatbot Views

Create `chatbot/views.py`:

```python
from django.shortcuts import render
from django.http import JsonResponse
from django.views.decorators.csrf import csrf_exempt
from django.conf import settings
import json
from openai import OpenAI

client = OpenAI(api_key=settings.OPENAI_API_KEY)

def chat_page(request):
    return render(request, 'chatbot/chat.html')

@csrf_exempt
def chatbot_api(request):
    if request.method == 'POST':
        try:
            data = json.loads(request.body)
            user_message = data.get('message', '')

            response = client.chat.completions.create(
                model="gpt-3.5-turbo",
                messages=[
                    {"role": "system", "content": "You are a helpful assistant for a blog website."},
                    {"role": "user", "content": user_message}
                ]
            )

            reply = response.choices[0].message.content
            return JsonResponse({"reply": reply})

        except Exception as e:
            return JsonResponse({"error": str(e)})

    return JsonResponse({"error": "Invalid request method"})
```

### Step 4: Chatbot Template

`templates/chatbot/chat.html`:

```html
<!DOCTYPE html>
<html>
<head>
    <title>AI Chatbot</title>
    <style>
        body { font-family: Arial; max-width: 600px; margin: 50px auto; padding: 20px; }
        #chat-box { border: 2px solid #ddd; height: 400px; overflow-y: auto; padding: 15px; margin-bottom: 15px; border-radius: 8px; background: #f9f9f9; }
        .user-msg { background: #3498db; color: white; padding: 10px; border-radius: 8px; margin: 5px 0; text-align: right; }
        .bot-msg { background: #2ecc71; color: white; padding: 10px; border-radius: 8px; margin: 5px 0; text-align: left; }
        input { width: 70%; padding: 12px; border: 2px solid #ddd; border-radius: 6px; }
        button { padding: 12px 25px; background: #3498db; color: white; border: none; border-radius: 6px; cursor: pointer; }
    </style>
</head>
<body>
    <h1>🤖 AI Chatbot</h1>
    <div id="chat-box"></div>
    <input type="text" id="message" placeholder="Type your message...">
    <button onclick="sendMessage()">Send</button>

    <script>
        async function sendMessage() {
            const input = document.getElementById('message');
            const message = input.value.trim();
            if (!message) return;

            const chatBox = document.getElementById('chat-box');
            chatBox.innerHTML += `<div class="user-msg"><b>You:</b> ${message}</div>`;
            input.value = '';

            try {
                const response = await fetch('/chatbot/', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ message: message })
                });

                const data = await response.json();

                if (data.reply) {
                    chatBox.innerHTML += `<div class="bot-msg"><b>Bot:</b> ${data.reply}</div>`;
                } else {
                    chatBox.innerHTML += `<div class="bot-msg" style="background:#e74c3c;"><b>Error:</b> ${data.error}</div>`;
                }
            } catch (error) {
                chatBox.innerHTML += `<div class="bot-msg" style="background:#e74c3c;"><b>Error:</b> ${error.message}</div>`;
            }

            chatBox.scrollTop = chatBox.scrollHeight;
        }

        document.getElementById('message').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') sendMessage();
        });
    </script>
</body>
</html>
```

---

## Part 3: Security Measures

### Security Checklist

```python
# settings.py - Security Settings

# SECURITY WARNING: keep the secret key used in production secret!
SECRET_KEY = os.environ.get('DJANGO_SECRET_KEY')

# SECURITY WARNING: don't run with debug turned on in production!
DEBUG = False

ALLOWED_HOSTS = ['your-domain.com', 'www.your-domain.com']

# Security Middleware
MIDDLEWARE = [
    'django.middleware.security.SecurityMiddleware',
    'django.contrib.sessions.middleware.SessionMiddleware',
    'django.middleware.common.CommonMiddleware',
    'django.middleware.csrf.CsrfViewMiddleware',
    'django.contrib.auth.middleware.AuthenticationMiddleware',
    'django.contrib.messages.middleware.MessageMiddleware',
    'django.middleware.clickjacking.XFrameOptionsMiddleware',
]

# HTTPS Settings
SECURE_SSL_REDIRECT = True
SESSION_COOKIE_SECURE = True
CSRF_COOKIE_SECURE = True
SECURE_BROWSER_XSS_FILTER = True
SECURE_CONTENT_TYPE_NOSNIFF = True
X_FRAME_OPTIONS = 'DENY'

# Password Validation
AUTH_PASSWORD_VALIDATORS = [
    {
        'NAME': 'django.contrib.auth.password_validation.UserAttributeSimilarityValidator',
    },
    {
        'NAME': 'django.contrib.auth.password_validation.MinimumLengthValidator',
        'OPTIONS': {'min_length': 8}
    },
    {
        'NAME': 'django.contrib.auth.password_validation.CommonPasswordValidator',
    },
    {
        'NAME': 'django.contrib.auth.password_validation.NumericPasswordValidator',
    },
]
```

---

## Part 4: Deployment

### Deploy to Heroku (PaaS)

1. **Create required files:**

`Procfile`:
```
web: gunicorn myproject.wsgi
```

`requirements.txt`:
```
Django>=4.0
gunicorn
openai
python-dotenv
whitenoise
psycopg2-binary
```

`runtime.txt`:
```
python-3.11.0
```

2. **Update settings for production:**

```python
# Add whitenoise
MIDDLEWARE = [
    'django.middleware.security.SecurityMiddleware',
    'whitenoise.middleware.WhiteNoiseMiddleware',  # Add this
    # ... rest
]

# Static files
STATIC_ROOT = BASE_DIR / 'staticfiles'
STATICFILES_STORAGE = 'whitenoise.storage.CompressedManifestStaticFilesStorage'

# Database
import dj_database_url
DATABASES = {
    'default': dj_database_url.config(
        default=os.environ.get('DATABASE_URL')
    )
}
```

3. **Deploy commands:**

```bash
# Install Heroku CLI
# Login
heroku login

# Create app
heroku create your-app-name

# Set environment variables
heroku config:set DJANGO_SECRET_KEY='your-secret-key'
heroku config:set OPENAI_API_KEY='your-openai-key'
heroku config:set DEBUG=False

# Push to Heroku
git push heroku main

# Run migrations
heroku run python manage.py migrate

# Create superuser
heroku run python manage.py createsuperuser

# Open app
heroku open
```

---

## Commands Summary

```bash
# Setup
pip install django openai python-dotenv gunicorn whitenoise psycopg2-binary

# Database
python manage.py makemigrations
python manage.py migrate

# Create admin
python manage.py createsuperuser

# Run locally
python manage.py runserver

# Deploy
git push heroku main
```
