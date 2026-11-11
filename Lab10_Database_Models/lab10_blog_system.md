# Lab 10: Database Models & Data Management

## Objective
Design and develop a smart Blog System using Django with full CRUD operations, logical restrictions, and proper relationships.

---

## Complete Blog System Implementation

### Step 1: Create App & Models

Create `blog/models.py`:

```python
from django.db import models
from django.core.exceptions import ValidationError

class Author(models.Model):
    name = models.CharField(max_length=100)
    email = models.EmailField(unique=True)
    bio = models.TextField(blank=True)
    created_at = models.DateTimeField(auto_now_add=True)

    def __str__(self):
        return self.name

class Post(models.Model):
    title = models.CharField(max_length=200)
    content = models.TextField()
    created_at = models.DateTimeField(auto_now_add=True)
    updated_at = models.DateTimeField(auto_now=True)
    is_published = models.BooleanField(default=False)
    author = models.ForeignKey(Author, on_delete=models.CASCADE, related_name='posts')

    def __str__(self):
        return self.title

    def comment_count(self):
        return self.comments.count()

    def can_add_comment(self):
        return self.is_published and self.comments.count() < 5

class Comment(models.Model):
    text = models.TextField()
    commenter_name = models.CharField(max_length=100)
    post = models.ForeignKey(Post, on_delete=models.CASCADE, related_name='comments')
    created_at = models.DateTimeField(auto_now_add=True)

    class Meta:
        ordering = ['-created_at']

    def __str__(self):
        return f"Comment by {self.commenter_name} on {self.post.title}"

    def clean(self):
        # Check if post is published
        if not self.post.is_published:
            raise ValidationError("Cannot comment on unpublished (draft) posts.")

        # Check comment limit
        if self.post.comments.count() >= 5:
            raise ValidationError("This post has reached the maximum of 5 comments.")

    def save(self, *args, **kwargs):
        self.clean()
        super().save(*args, **kwargs)
```

### Step 2: Register Models in Admin

`blog/admin.py`:

```python
from django.contrib import admin
from .models import Author, Post, Comment

@admin.register(Author)
class AuthorAdmin(admin.ModelAdmin):
    list_display = ['name', 'email', 'created_at']
    search_fields = ['name', 'email']

@admin.register(Post)
class PostAdmin(admin.ModelAdmin):
    list_display = ['title', 'author', 'is_published', 'created_at', 'comment_count']
    list_filter = ['is_published', 'created_at']
    search_fields = ['title', 'content']

@admin.register(Comment)
class CommentAdmin(admin.ModelAdmin):
    list_display = ['commenter_name', 'post', 'created_at']
    list_filter = ['created_at']
```

### Step 3: Create Views with CRUD + Restrictions

`blog/views.py`:

```python
from django.shortcuts import render, get_object_or_404, redirect
from django.http import HttpResponse, JsonResponse
from django.contrib import messages
from .models import Author, Post, Comment

# ================================
# AUTHOR CRUD
# ================================

def author_list(request):
    authors = Author.objects.all()
    return render(request, 'blog/author_list.html', {'authors': authors})

def author_create(request):
    if request.method == 'POST':
        name = request.POST.get('name')
        email = request.POST.get('email')
        bio = request.POST.get('bio', '')

        if name and email:
            Author.objects.create(name=name, email=email, bio=bio)
            messages.success(request, 'Author created successfully!')
            return redirect('author_list')
        else:
            messages.error(request, 'Name and email are required!')

    return render(request, 'blog/author_form.html')

def author_update(request, pk):
    author = get_object_or_404(Author, pk=pk)

    if request.method == 'POST':
        author.name = request.POST.get('name')
        author.email = request.POST.get('email')
        author.bio = request.POST.get('bio', '')
        author.save()
        messages.success(request, 'Author updated successfully!')
        return redirect('author_list')

    return render(request, 'blog/author_form.html', {'author': author})

def author_delete(request, pk):
    author = get_object_or_404(Author, pk=pk)

    if request.method == 'POST':
        author.delete()
        messages.success(request, 'Author and all related posts/comments deleted!')
        return redirect('author_list')

    return render(request, 'blog/author_confirm_delete.html', {'author': author})

# ================================
# POST CRUD
# ================================

def post_list(request):
    posts = Post.objects.all()
    return render(request, 'blog/post_list.html', {'posts': posts})

def post_detail(request, pk):
    post = get_object_or_404(Post, pk=pk)
    can_comment = post.can_add_comment()
    comments = post.comments.all()

    return render(request, 'blog/post_detail.html', {
        'post': post,
        'comments': comments,
        'can_comment': can_comment
    })

def post_create(request):
    authors = Author.objects.all()

    if request.method == 'POST':
        title = request.POST.get('title')
        content = request.POST.get('content')
        author_id = request.POST.get('author')
        is_published = request.POST.get('is_published') == 'on'

        if title and content and author_id:
            author = get_object_or_404(Author, pk=author_id)
            Post.objects.create(
                title=title,
                content=content,
                author=author,
                is_published=is_published
            )
            messages.success(request, 'Post created successfully!')
            return redirect('post_list')
        else:
            messages.error(request, 'All fields are required!')

    return render(request, 'blog/post_form.html', {'authors': authors})

def post_update(request, pk):
    post = get_object_or_404(Post, pk=pk)
    authors = Author.objects.all()

    if request.method == 'POST':
        post.title = request.POST.get('title')
        post.content = request.POST.get('content')
        post.is_published = request.POST.get('is_published') == 'on'
        post.save()
        messages.success(request, 'Post updated successfully!')
        return redirect('post_list')

    return render(request, 'blog/post_form.html', {'post': post, 'authors': authors})

def post_delete(request, pk):
    post = get_object_or_404(Post, pk=pk)

    if request.method == 'POST':
        post.delete()
        messages.success(request, 'Post and all comments deleted!')
        return redirect('post_list')

    return render(request, 'blog/post_confirm_delete.html', {'post': post})

# ================================
# COMMENT CRUD with Restrictions
# ================================

def comment_create(request, post_pk):
    post = get_object_or_404(Post, pk=post_pk)

    # Check if post is published
    if not post.is_published:
        messages.error(request, 'Cannot comment on draft posts!')
        return redirect('post_detail', pk=post_pk)

    # Check comment limit
    if post.comments.count() >= 5:
        messages.error(request, 'This post has reached the maximum of 5 comments!')
        return redirect('post_detail', pk=post_pk)

    if request.method == 'POST':
        text = request.POST.get('text')
        commenter_name = request.POST.get('commenter_name')

        if text and commenter_name:
            Comment.objects.create(
                text=text,
                commenter_name=commenter_name,
                post=post
            )
            messages.success(request, 'Comment added successfully!')
        else:
            messages.error(request, 'All fields are required!')

    return redirect('post_detail', pk=post_pk)

def comment_delete(request, pk):
    comment = get_object_or_404(Comment, pk=pk)
    post_pk = comment.post.pk

    if request.method == 'POST':
        comment.delete()
        messages.success(request, 'Comment deleted!')

    return redirect('post_detail', pk=post_pk)
```

### Step 4: URL Configuration

`blog/urls.py`:

```python
from django.urls import path
from . import views

urlpatterns = [
    # Author URLs
    path('authors/', views.author_list, name='author_list'),
    path('authors/create/', views.author_create, name='author_create'),
    path('authors/<int:pk>/update/', views.author_update, name='author_update'),
    path('authors/<int:pk>/delete/', views.author_delete, name='author_delete'),

    # Post URLs
    path('posts/', views.post_list, name='post_list'),
    path('posts/<int:pk>/', views.post_detail, name='post_detail'),
    path('posts/create/', views.post_create, name='post_create'),
    path('posts/<int:pk>/update/', views.post_update, name='post_update'),
    path('posts/<int:pk>/delete/', views.post_delete, name='post_delete'),

    # Comment URLs
    path('posts/<int:post_pk>/comment/', views.comment_create, name='comment_create'),
    path('comments/<int:pk>/delete/', views.comment_delete, name='comment_delete'),
]
```

### Step 5: Templates

`templates/blog/base.html`:

```html
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{% block title %}Blog System{% endblock %}</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; max-width: 1000px; margin: 0 auto; padding: 20px; background: #f5f5f5; }
        .container { background: white; padding: 30px; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.1); }
        nav { background: #2c3e50; padding: 15px; border-radius: 8px; margin-bottom: 20px; }
        nav a { color: white; text-decoration: none; margin-right: 20px; font-weight: 600; }
        nav a:hover { color: #3498db; }
        .messages { margin-bottom: 20px; }
        .message { padding: 12px; border-radius: 6px; margin-bottom: 10px; }
        .success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .warning { background: #fff3cd; color: #856404; border: 1px solid #ffeaa7; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #3498db; color: white; }
        tr:hover { background: #f5f5f5; }
        .btn { padding: 8px 16px; border: none; border-radius: 5px; cursor: pointer; text-decoration: none; display: inline-block; margin: 2px; }
        .btn-primary { background: #3498db; color: white; }
        .btn-success { background: #2ecc71; color: white; }
        .btn-danger { background: #e74c3c; color: white; }
        .btn-warning { background: #f39c12; color: white; }
        .badge { padding: 4px 8px; border-radius: 4px; font-size: 0.85rem; }
        .badge-success { background: #2ecc71; color: white; }
        .badge-warning { background: #f39c12; color: white; }
        .restricted { background: #ffebee; padding: 15px; border-radius: 8px; border-left: 4px solid #e74c3c; color: #c0392b; }
    </style>
</head>
<body>
    <div class="container">
        <nav>
            <a href="{% url 'author_list' %}">Authors</a>
            <a href="{% url 'post_list' %}">Posts</a>
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

`templates/blog/post_list.html`:

```html
{% extends 'blog/base.html' %}

{% block title %}All Posts{% endblock %}

{% block content %}
<h1>Blog Posts</h1>
<a href="{% url 'post_create' %}" class="btn btn-success">+ Create New Post</a>

<table>
    <thead>
        <tr>
            <th>Title</th>
            <th>Author</th>
            <th>Status</th>
            <th>Comments</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        {% for post in posts %}
        <tr>
            <td><a href="{% url 'post_detail' post.pk %}">{{ post.title }}</a></td>
            <td>{{ post.author.name }}</td>
            <td>
                {% if post.is_published %}
                <span class="badge badge-success">Published</span>
                {% else %}
                <span class="badge badge-warning">Draft</span>
                {% endif %}
            </td>
            <td>{{ post.comment_count }}/5</td>
            <td>
                <a href="{% url 'post_update' post.pk %}" class="btn btn-primary">Edit</a>
                <a href="{% url 'post_delete' post.pk %}" class="btn btn-danger">Delete</a>
            </td>
        </tr>
        {% endfor %}
    </tbody>
</table>
{% endblock %}
```

`templates/blog/post_detail.html`:

```html
{% extends 'blog/base.html' %}

{% block title %}{{ post.title }}{% endblock %}

{% block content %}
<h1>{{ post.title }}</h1>
<p><strong>Author:</strong> {{ post.author.name }}</p>
<p><strong>Status:</strong> 
    {% if post.is_published %}
    <span class="badge badge-success">Published</span>
    {% else %}
    <span class="badge badge-warning">Draft</span>
    {% endif %}
</p>

<div style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0;">
    {{ post.content|linebreaks }}
</div>

<h2>Comments ({{ comments.count }}/5 max)</h2>

{% if can_comment %}
    <form method="POST" action="{% url 'comment_create' post.pk %}" style="margin-bottom: 20px;">
        {% csrf_token %}
        <div style="margin-bottom: 10px;">
            <input type="text" name="commenter_name" placeholder="Your Name" required style="padding: 10px; width: 200px;">
        </div>
        <div style="margin-bottom: 10px;">
            <textarea name="text" placeholder="Your comment..." required rows="3" style="padding: 10px; width: 100%;"></textarea>
        </div>
        <button type="submit" class="btn btn-success">Add Comment</button>
    </form>
{% else %}
    <div class="restricted">
        {% if not post.is_published %}
            ⚠️ Comments are disabled on draft posts.
        {% else %}
            ⚠️ This post has reached the maximum of 5 comments.
        {% endif %}
    </div>
{% endif %}

{% for comment in comments %}
<div style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin-bottom: 10px;">
    <strong>{{ comment.commenter_name }}</strong> 
    <small style="color: #666;">{{ comment.created_at|date:"M d, Y H:i" }}</small>
    <p>{{ comment.text }}</p>
    <form method="POST" action="{% url 'comment_delete' comment.pk %}" style="display: inline;">
        {% csrf_token %}
        <button type="submit" class="btn btn-danger" style="padding: 4px 8px; font-size: 0.8rem;">Delete</button>
    </form>
</div>
{% empty %}
<p>No comments yet.</p>
{% endfor %}

<a href="{% url 'post_list' %}" class="btn btn-primary">← Back to Posts</a>
{% endblock %}
```

### Step 6: Main URL Configuration

Update `myproject/urls.py`:

```python
from django.contrib import admin
from django.urls import path, include

urlpatterns = [
    path('admin/', admin.site.urls),
    path('blog/', include('blog.urls')),
]
```

### Step 7: Commands to Run

```bash
# Make migrations
python manage.py makemigrations

# Apply migrations
python manage.py migrate

# Create superuser
python manage.py createsuperuser

# Run server
python manage.py runserver
```

---

## System Features & Restrictions

| Feature | Implementation |
|---------|-----------------|
| One Author → Many Posts | ForeignKey with CASCADE |
| One Post → Many Comments | ForeignKey with CASCADE |
| Max 5 Comments | Validation in Comment.clean() |
| Draft Posts Block Comments | is_published check |
| Author Delete → Posts Delete | on_delete=models.CASCADE |
| Post Delete → Comments Delete | on_delete=models.CASCADE |
| Error Messages | Django messages framework |

---

## Test URLs
- `/blog/authors/` - List authors
- `/blog/posts/` - List posts
- `/blog/posts/1/` - View post detail & comments
- `/admin/` - Manage all data
