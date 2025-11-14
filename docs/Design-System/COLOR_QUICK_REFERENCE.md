# Quick Color Reference Guide

## 🎨 Core Brand Colors

```css
/* Primary Blue (Logo) */
--brand-primary: #0066B2
--brand-primary-light: #3A8FC7
--brand-primary-dark: #004D85

/* Secondary Gray (Logo) */
--brand-secondary: #455A64
--brand-secondary-light: #607D8B
```

## 🔧 Most Used CSS Variables

### Buttons
```css
background: var(--gradient-primary);  /* Blue gradient */
color: var(--white);
box-shadow: var(--shadow-sm);
```

### Cards
```css
background: var(--white);
border: 1px solid var(--border-primary);
box-shadow: var(--shadow-sm);
border-radius: var(--radius-lg);
```

### Text
```css
color: var(--text-primary);     /* Dark text */
color: var(--text-light);       /* Light text */
color: var(--brand-primary);    /* Brand color text */
```

### Backgrounds
```css
background: var(--bg-light);    /* Light gray */
background: var(--bg-gray);     /* Blue-gray */
background: var(--gradient-primary); /* Blue gradient */
```

## 🎯 Utility Classes

### Background Colors
```html
<div class="bg-primary">Blue background</div>
<div class="bg-gradient-primary">Blue gradient</div>
<div class="bg-light">Light background</div>
```

### Text Colors
```html
<span class="text-primary">Blue text</span>
<span class="text-light">Light text</span>
<span class="text-success">Success text</span>
```

### Shadows
```html
<div class="shadow-sm">Small shadow</div>
<div class="shadow-md">Medium shadow</div>
<div class="shadow-lg">Large shadow</div>
```

## 📱 Common Patterns

### Primary Button
```css
.btn-primary {
    background: var(--gradient-primary);
    color: var(--white);
    padding: 0.7rem 1.2rem;
    border-radius: var(--radius-md);
    border: none;
    box-shadow: var(--shadow-sm);
    transition: var(--transition-base);
}

.btn-primary:hover {
    box-shadow: var(--shadow-md);
    transform: translateY(-2px);
}
```

### Card Component
```css
.card {
    background: var(--white);
    border: 1px solid var(--border-primary);
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-sm);
    padding: 1.5rem;
    transition: var(--transition-base);
}

.card:hover {
    box-shadow: var(--shadow-md);
    transform: translateY(-3px);
}
```

### Link Style
```css
a {
    color: var(--brand-primary);
    text-decoration: none;
    transition: var(--transition-base);
}

a:hover {
    color: var(--brand-primary-dark);
    text-decoration: underline;
}
```

## 🎨 Color Opacity Values

Use these for backgrounds and overlays:

```css
/* Primary Blue with opacity */
rgba(0, 102, 178, 0.05)  /* 5% - Very light background */
rgba(0, 102, 178, 0.10)  /* 10% - Light background */
rgba(0, 102, 178, 0.15)  /* 15% - Border/subtle elements */
rgba(0, 102, 178, 0.20)  /* 20% - Hover states */
rgba(0, 102, 178, 0.35)  /* 35% - Focus rings */
```

## ⚡ Quick Replacements

Replace old colors with new ones:

| Old Color | New Color | Variable |
|-----------|-----------|----------|
| `#2D9999` | `#0066B2` | `var(--brand-primary)` |
| `#00D9D9` | `#3A8FC7` | `var(--brand-primary-light)` |
| `#20b2aa` | `#0066B2` | `var(--brand-primary)` |
| `#3498db` | `#3A8FC7` | `var(--brand-primary-light)` |

## 🌙 Dark Theme

```css
:root[data-theme="dark"] {
    --bg-white: #0F172A;
    --bg-light: #1E293B;
    --text-primary: #E6F0FF;
}
```

## 📋 Import Statement

Add to your CSS files:

```css
@import url('./colors-unified.css');
```

Or in HTML:

```html
<link rel="stylesheet" href="/assets/css/colors-unified.css">
```

## ✅ Best Practices

1. **Always use CSS variables** instead of hardcoded hex values
2. **Maintain contrast ratios** for accessibility (minimum 4.5:1)
3. **Use semantic colors** for status indicators (success, warning, error)
4. **Apply shadows consistently** using predefined shadow variables
5. **Test in dark theme** if applicable

## 🔍 Finding Colors in Code

Search for these patterns to update old colors:

```
#2D9999
#00D9D9
#20b2aa
#4dd0d0
rgba(45, 153, 153
```

Replace with:

```
var(--brand-primary)
var(--brand-primary-light)
var(--gradient-primary)
rgba(0, 102, 178
```

---

**Quick Help:**
- 🎨 Main colors file: `/public/assets/css/colors-unified.css`
- 📖 Full documentation: `/docs/LOGO_COLOR_SYSTEM.md`
- 🔧 Need help? Check the design system docs
