# Lab 8: Python & Django Setup Guide

## Objective
Write Python scripts demonstrating lists, dictionaries, and install Django.

---

## Task 1: Install Django

### Prerequisites
- Python 3.10+ installed
- pip (Python package manager)

### Step 1: Create Virtual Environment
```bash
# Windows
python -m venv venv
venv\Scriptsctivate

# macOS / Linux
python3 -m venv venv
source venv/bin/activate
```

### Step 2: Install Django
```bash
pip install django

# Verify installation
django-admin --version
# Expected output: 4.x or 5.x
```

---

## Task 2: Python Lists & Dictionaries Demo

Create a file `python_basics.py`:

```python
# ============================================
# LAB 8: Python Lists and Dictionaries
# ============================================

# LISTS Demonstration
print("=" * 50)
print("PYTHON LISTS")
print("=" * 50)

# Creating lists
fruits = ["Apple", "Banana", "Cherry", "Date", "Elderberry"]
numbers = [10, 25, 30, 45, 50, 65]
mixed = [1, "hello", 3.14, True, [1, 2, 3]]

print(f"Fruits: {fruits}")
print(f"Numbers: {numbers}")
print(f"Mixed: {mixed}")

# List operations
print("
--- List Operations ---")
fruits.append("Fig")           # Add item
print(f"After append: {fruits}")

fruits.insert(2, "Blueberry")  # Insert at index
print(f"After insert: {fruits}")

fruits.remove("Banana")        # Remove item
print(f"After remove: {fruits}")

popped = fruits.pop()          # Remove last
print(f"Popped: {popped}, List: {fruits}")

print(f"Length: {len(fruits)}")
print(f"Index of Cherry: {fruits.index('Cherry')}")
print(f"Slice [1:4]: {fruits[1:4]}")

fruits.sort()                  # Sort
print(f"Sorted: {fruits}")

fruits.reverse()               # Reverse
print(f"Reversed: {fruits}")

# List comprehension
squares = [x**2 for x in range(1, 6)]
print(f"Squares: {squares}")

# ============================================
# DICTIONARIES Demonstration
# ============================================
print("
" + "=" * 50)
print("PYTHON DICTIONARIES")
print("=" * 50)

# Creating dictionaries
student = {
    "name": "Hafsa Nayyab",
    "age": 21,
    "course": "BS Software Engineering",
    "university": "GIKI",
    "skills": ["Python", "Django", "HTML", "CSS", "JavaScript"]
}

print(f"Student: {student}")

# Accessing values
print(f"
Name: {student['name']}")
print(f"Age: {student.get('age')}")
print(f"Skills: {student['skills']}")

# Dictionary operations
print("
--- Dictionary Operations ---")
student["cgpa"] = 3.85         # Add new key
print(f"After adding CGPA: {student}")

student["age"] = 22            # Update value
print(f"After updating age: {student}")

del student["cgpa"]            # Delete key
print(f"After deleting CGPA: {student}")

# Dictionary methods
print(f"Keys: {list(student.keys())}")
print(f"Values: {list(student.values())}")
print(f"Items: {list(student.items())}")

# Iterating
print("
--- Iterating Dictionary ---")
for key, value in student.items():
    print(f"{key}: {value}")

# Nested dictionary
classroom = {
    "student1": {"name": "Ali", "marks": 85},
    "student2": {"name": "Sara", "marks": 92},
    "student3": {"name": "John", "marks": 78}
}

print(f"
Classroom: {classroom}")
print(f"Student 1 marks: {classroom['student1']['marks']}")

# Dictionary comprehension
grades = {k: v["marks"] for k, v in classroom.items()}
print(f"Grades: {grades}")

print("
" + "=" * 50)
print("LAB 8 COMPLETE")
print("=" * 50)
```

Run with: `python python_basics.py`

---

## Task 3: Create Django Project

```bash
# Create project
django-admin startproject lab8project

# Navigate to project
cd lab8project

# Run development server
python manage.py runserver

# Open browser: http://127.0.0.1:8000
```

---

## Task 4: Create Django App

```bash
# Create app
python manage.py startapp students

# Register app in lab8project/settings.py:
INSTALLED_APPS = [
    'django.contrib.admin',
    'django.contrib.auth',
    ...
    'students',  # ADD THIS
]

# Run server again
python manage.py runserver
```

---

## Project Structure
```
lab8project/
├── manage.py
├── lab8project/
│   ├── __init__.py
│   ├── settings.py
│   ├── urls.py
│   ├── asgi.py
│   └── wsgi.py
└── students/
    ├── __init__.py
    ├── admin.py
    ├── apps.py
    ├── models.py
    ├── tests.py
    └── views.py
```

---

## Submission Checklist
- [ ] Python lists demonstration script
- [ ] Python dictionaries demonstration script
- [ ] Django installed and verified
- [ ] Project `lab8project` created
- [ ] App `students` created and registered
- [ ] Development server running successfully
- [ ] Screenshots of working application
