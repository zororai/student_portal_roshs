# Tailwind Dashboard Design Specification

## Project: Rose of Sharon Student Portal

This document defines the **UI/UX design specification** for the dashboard layout shown in the provided design preview. It serves as a **single source of truth** for implementing the design using **Tailwind CSS**, while maintaining **existing routes and application logic**.

---

## 🎯 DESIGN GOALS

- Clean, modern education-focused dashboard
- Professional and trustworthy appearance
- Mobile-first and fully responsive
- Consistent visual hierarchy
- Lightweight and fast UI using Tailwind CSS

---

## 🚫 NON-NEGOTIABLE CONSTRAINTS

- ❌ **Do NOT change any routes, URLs, links, or button actions**
- ❌ **Do NOT modify backend controllers or logic**
- ❌ **Do NOT rename navigation items**

✅ UI changes must be limited to **Tailwind classes, layout structure, and responsive behavior**

---

## 🎨 COLOR SYSTEM

### Primary Palette

| Purpose | Tailwind Class |
|------|------|
| Primary | `bg-blue-600` |
| Primary Hover | `hover:bg-blue-700` |
| Accent | `bg-indigo-500` |
| Success | `bg-green-500` |
| Danger | `bg-red-500` |

### Neutral Palette

| Usage | Class |
|------|------|
| Page Background | `bg-slate-50` |
| Card Background | `bg-white` |
| Borders | `border-slate-100` |
| Primary Text | `text-slate-800` |
| Secondary Text | `text-slate-400` |

---

## 🧭 TOP NAVBAR

### Design Rules

- Fixed height
- Full width
- Gradient or solid primary color
- Right-aligned user dropdown

### Tailwind Reference

```html
<header class="bg-gradient-to-r from-blue-600 to-blue-500 text-white h-16 flex items-center px-6">
```

---

## 🧭 SIDEBAR DESIGN

### Layout

- Fixed on desktop
- Collapsible on tablet
- Hidden behind hamburger menu on mobile

### Styling

```html
<aside class="bg-white border-r border-slate-200 w-64">
```

### Navigation Items

```html
<a class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-slate-100">
```

### Active State

```html
bg-blue-50 text-blue-600 font-medium
```

---

## 📊 DASHBOARD STAT CARDS

### Layout

```html
grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-6
```

### Card Styling

```html
<div class="bg-white rounded-xl shadow-sm border border-slate-100 p-5 hover:shadow-md transition">
```

### Typography

```html
<p class="text-sm text-slate-400">Label</p>
<h2 class="text-3xl font-bold text-slate-800">Value</h2>
```

---

## 📈 ANALYTICS & CHART SECTION

### Container

```html
<div class="bg-white rounded-xl shadow-sm p-6">
```

### Section Header

```html
<h3 class="text-lg font-semibold text-slate-800">Student Results by Gender</h3>
<p class="text-sm text-slate-400">Pass/Fail distribution</p>
```

### Chart Rules

- Responsive width only
- No fixed height
- Wrapped in overflow-safe container

---

## 🧑‍🎓 SUMMARY PANELS (Male / Female Results)

### Card Style

```html
<div class="rounded-xl border p-4 flex items-center gap-4">
```

### Male Variant

```html
border-blue-200 bg-blue-50
```

### Female Variant

```html
border-pink-200 bg-pink-50
```

---

## 📋 TABLE DESIGN RULES

### Wrapper

```html
<div class="bg-white rounded-xl shadow-sm overflow-x-auto">
```

### Header

```html
bg-slate-50 text-slate-600 text-sm uppercase
```

### Rows

```html
hover:bg-slate-50 transition
```

---

## ✋ BUTTON SYSTEM

### Primary Button

```html
bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition
```

### Secondary Button

```html
bg-slate-100 text-slate-700 px-4 py-2 rounded-lg
```

### Destructive Button

```html
bg-red-500 hover:bg-red-600 text-white
```

---

## 📱 RESPONSIVE BEHAVIOR

### Breakpoints

| Device | Width |
|------|------|
| Mobile | ≤ 576px |
| Tablet | 577px – 992px |
| Desktop | ≥ 993px |

### Rules

- Cards stack vertically on mobile
- Sidebar hidden on mobile
- Charts scroll horizontally if needed
- Touch targets ≥ 44px

---

## 🧪 QUALITY CHECKLIST

- ✅ Consistent spacing
- ✅ Rounded corners
- ✅ Soft shadows only
- ✅ Clear typography hierarchy
- ✅ No visual clutter
- ✅ No broken routes

---

## ✅ ACCEPTANCE CRITERIA

The design is approved only if:

- UI matches the provided preview
- Fully responsive across devices
- Tailwind-only styling
- Routes and logic remain unchanged

---

## 📌 FINAL NOTE

> This design prioritizes **clarity, usability, and performance**.
>
> Any deviation from this specification requires approval.

---

**End of Design Specification**

