# Lab 9: Django Interface and Project

## Objective
Create a basic project using Django, understanding the Django interface, project structure, and key components (apps, views, templates, static files).

---

## Complete Project Setup

### Step 1: Create Virtual Environment & Install Django
```bash
python -m venv venv
source venv/bin/activate  # Linux/Mac
venv\Scriptsctivate     # Windows

pip install django
```

### Step 2: Create Project
```bash
django-admin startproject myportfolio
cd myportfolio
```

### Step 3: Create App
```bash
python manage.py startapp pages
```

### Step 4: Register App in settings.py

Edit `myportfolio/settings.py`:

```python
INSTALLED_APPS = [
    'django.contrib.admin',
    'django.contrib.auth',
    'django.contrib.contenttypes',
    'django.contrib.sessions',
    'django.contrib.messages',
    'django.contrib.staticfiles',
    'pages',  # <-- ADD THIS
]

# Add template directory
import os
TEMPLATES = [
    {
        'BACKEND': 'django.template.backends.django.DjangoTemplates',
        'DIRS': [os.path.join(BASE_DIR, 'templates')],  # <-- ADD THIS
        'APP_DIRS': True,
        'OPTIONS': {
            'context_processors': [
                'django.template.context_processors.debug',
                'django.template.context_processors.request',
                'django.contrib.auth.context_processors.auth',
                'django.contrib.messages.context_processors.messages',
            ],
        },
    },
]

# Static files configuration
STATIC_URL = '/static/'
STATICFILES_DIRS = [
    os.path.join(BASE_DIR, 'static'),
]
```

### Step 5: Create URL Configuration

Create `pages/urls.py`:

```python
from django.urls import path
from . import views

urlpatterns = [
    path('', views.home, name='home'),
    path('about/', views.about, name='about'),
    path('contact/', views.contact, name='contact'),
]
```

Update `myportfolio/urls.py`:

```python
from django.contrib import admin
from django.urls import path, include

urlpatterns = [
    path('admin/', admin.site.urls),
    path('', include('pages.urls')),  # <-- ADD THIS
]
```

### Step 6: Create Views

Edit `pages/views.py`:

```python
from django.shortcuts import render

def home(request):
    context = {
        'title': 'Welcome to My Portfolio',
        'name': 'Hafsa Nayyab',
        'skills': ['Python', 'Django', 'JavaScript', 'HTML/CSS', 'PostgreSQL'],
        'projects': [
            {'name': 'Weather App', 'desc': 'Real-time weather using OpenWeather API'},
            {'name': 'Blog Platform', 'desc': 'Full-featured blog with CRUD operations'},
            {'name': 'Task Manager', 'desc': 'Kanban-style productivity application'},
        ]
    }
    return render(request, 'home.html', context)

def about(request):
    context = {
        'title': 'About Me',
        'bio': 'I am a passionate developer with 3 years of experience.',
    }
    return render(request, 'about.html', context)

def contact(request):
    context = {'title': 'Contact Me'}
    return render(request, 'contact.html', context)
```

### Step 7: Create Templates

Create directories:
```bash
mkdir templates
mkdir -p static/css static/js static/images
```

Create `templates/base.html`:

```html
{% load static %}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{% block title %}My Portfolio{% endblock %}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{% static 'css/style.css' %}">
</head>
<body>
    <nav class="navbar">
        <div class="nav-brand">MyPortfolio</div>
        <ul class="nav-links">
            <li><a href="{% url 'home' %}">Home</a></li>
            <li><a href="{% url 'about' %}">About</a></li>
            <li><a href="{% url 'contact' %}">Contact</a></li>
        </ul>
    </nav>

    <main class="main-content">
        {% block content %}{% endblock %}
    </main>

    <footer class="footer">
        <p>&copy; 2026 My Portfolio. Built with Django.</p>
    </footer>

    <script src="{% static 'js/main.js' %}"></script>
</body>
</html>
```

Create `templates/home.html`:

```html
{% extends 'base.html' %}
{% load static %}

{% block title %}{{ title }}{% endblock %}

{% block content %}
<section class="hero">
    <div class="hero-content">
        <h1>Hello, I'm <span class="highlight">{{ name }}</span></h1>
        <p class="hero-subtitle">Full Stack Developer | Django Enthusiast</p>
        <a href="{% url 'contact' %}" class="btn btn-primary">Get In Touch</a>
    </div>
</section>

<section class="section">
    <h2>My Skills</h2>
    <div class="skills-grid">
        {% for skill in skills %}
        <span class="skill-badge">{{ skill }}</span>
        {% endfor %}
    </div>
</section>

<section class="section">
    <h2>My Projects</h2>
    <div class="projects-grid">
        {% for project in projects %}
        <div class="project-card">
            <h3>{{ project.name }}</h3>
            <p>{{ project.desc }}</p>
        </div>
        {% endfor %}
    </div>
</section>
{% endblock %}
```

Create `templates/about.html`:

```html
{% extends 'base.html' %}

{% block title %}{{ title }}{% endblock %}

{% block content %}
<section class="section">
    <h1>{{ title }}</h1>
    <p>{{ bio }}</p>
    <p>I love building things for the web and learning new technologies.</p>
</section>
{% endblock %}
```

Create `templates/contact.html`:

```html
{% extends 'base.html' %}

{% block title %}{{ title }}{% endblock %}

{% block content %}
<section class="section">
    <h1>{{ title }}</h1>
    <form class="contact-form">
        {% csrf_token %}
        <div class="form-group">
            <label for="name">Your Name</label>
            <input type="text" id="name" name="name" required>
        </div>
        <div class="form-group">
            <label for="email">Email Address</label>
            <input type="email" id="email" name="email" required>
        </div>
        <div class="form-group">
            <label for="message">Message</label>
            <textarea id="message" name="message" rows="5"></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Send Message</button>
    </form>
</section>
{% endblock %}
```

### Step 8: Create CSS

Create `static/css/style.css`:

```css
* { margin: 0; padding: 0; box-sizing: border-box; }

body {
    font-family: 'Inter', sans-serif;
    background-color: #f8f9fa;
    color: #1a1a1a;
    line-height: 1.6;
}

.navbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background-color: #092E20;
    padding: 1rem 2rem;
    position: sticky;
    top: 0;
    z-index: 100;
}

.nav-brand { color: #44B78B; font-size: 1.5rem; font-weight: 700; }

.nav-links {
    list-style: none;
    display: flex;
    gap: 2rem;
}

.nav-links a {
    color: #ffffff;
    text-decoration: none;
    font-weight: 500;
    transition: color 0.3s;
}

.nav-links a:hover { color: #44B78B; }

.hero {
    background: linear-gradient(135deg, #092E20 0%, #2D6A4F 100%);
    color: white;
    padding: 5rem 2rem;
    text-align: center;
}

.hero h1 { font-size: 3rem; margin-bottom: 1rem; }
.highlight { color: #44B78B; }
.hero-subtitle { font-size: 1.25rem; opacity: 0.85; margin-bottom: 2rem; }

.btn {
    display: inline-block;
    padding: 0.75rem 2rem;
    border-radius: 6px;
    font-weight: 600;
    text-decoration: none;
    cursor: pointer;
    border: none;
    font-size: 1rem;
    transition: transform 0.2s, box-shadow 0.2s;
}

.btn:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.2); }
.btn-primary { background-color: #44B78B; color: #092E20; }

.section {
    max-width: 1100px;
    margin: 0 auto;
    padding: 3rem 2rem;
}

.section h2 {
    font-size: 2rem;
    color: #092E20;
    margin-bottom: 1.5rem;
    padding-bottom: 0.5rem;
    border-bottom: 3px solid #44B78B;
}

.skills-grid {
    display: flex;
    flex-wrap: wrap;
    gap: 0.75rem;
}

.skill-badge {
    background: #092E20;
    color: #44B78B;
    padding: 0.4rem 1rem;
    border-radius: 20px;
    font-size: 0.9rem;
    font-weight: 600;
}

.projects-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 1.5rem;
}

.project-card {
    background: white;
    border-radius: 10px;
    padding: 1.5rem;
    box-shadow: 0 2px 12px rgba(0,0,0,0.08);
    border-top: 4px solid #44B78B;
}

.project-card h3 { color: #092E20; margin-bottom: 0.5rem; }

.contact-form { max-width: 600px; }
.form-group { margin-bottom: 1.25rem; }
.form-group label { display: block; font-weight: 600; margin-bottom: 0.4rem; color: #092E20; }
.form-group input, .form-group textarea {
    width: 100%;
    padding: 0.75rem;
    border: 2px solid #d1d5db;
    border-radius: 6px;
    font-size: 1rem;
}

.footer {
    background-color: #092E20;
    color: #aaa;
    text-align: center;
    padding: 1.5rem;
    margin-top: 3rem;
}

@media (max-width: 768px) {
    .hero h1 { font-size: 2rem; }
    .nav-links { gap: 1rem; }
}
```

### Step 9: Run Migrations and Server

```bash
python manage.py migrate
python manage.py runserver
```

Visit:
- http://127.0.0.1:8000/ → Home
- http://127.0.0.1:8000/about/ → About
- http://127.0.0.1:8000/contact/ → Contact
- http://127.0.0.1:8000/admin/ → Admin

---

## Lab Tasks

### Task 1: Add Skills Page
- Add `skills/` URL in `pages/urls.py`
- Create `skills` view with categories
- Create `templates/skills.html`
- Add link in navbar

### Task 2: Dynamic Project Cards
- Add `Project` model in `models.py`
- Run migrations
- Register in admin
- Create projects via admin
- Update home view to query database

### Task 3: Contact Form Handling
- Create `ContactMessage` model
- Handle POST in contact view
- Validate server-side
- Show success/error messages

---

## Project Structure
```
myportfolio/
├── manage.py
├── myportfolio/
│   ├── __init__.py
│   ├── settings.py
│   ├── urls.py
│   ├── asgi.py
│   └── wsgi.py
├── pages/
│   ├── __init__.py
│   ├── admin.py
│   ├── apps.py
│   ├── models.py
│   ├── tests.py
│   ├── urls.py
│   └── views.py
├── templates/
│   ├── base.html
│   ├── home.html
│   ├── about.html
│   └── contact.html
└── static/
    ├── css/
    │   └── style.css
    └── js/
        └── main.js
```
