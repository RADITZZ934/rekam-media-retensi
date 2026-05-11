# Vue Data Pages - Consistent Table Styling Patterns

## Analysis Summary
Examined 4 data pages: **DataKasus.vue**, **DataUser.vue**, **DataRetensi.vue**, **DataPasien.vue**

All pages follow a highly consistent design pattern with Tailwind CSS. Below is the complete breakdown of styling, layout, and component structures.

---

## 1. PAGE LAYOUT & STRUCTURE

### Main Container
```html
<div class="min-h-screen bg-gray-50 p-8">
```
- **Full height**: `min-h-screen`
- **Background**: `bg-gray-50` (light gray, consistent across all)
- **Padding**: `p-8` (32px on all sides)

### Page Header Section
```html
<div class="mb-8">
  <h1 class="text-3xl font-bold text-gray-900">Page Title</h1>
  <p class="text-gray-600 mt-2">Descriptive subtitle</p>
</div>
```
- **Title**: `text-3xl font-bold text-gray-900`
- **Subtitle**: `text-gray-600 mt-2`
- **Bottom margin**: `mb-8`

---

## 2. ACTION BAR / FILTER SECTION

### Container Styling
```html
<div class="bg-white rounded-lg shadow-sm p-6 mb-6">
```
- **Background**: `bg-white`
- **Border radius**: `rounded-lg`
- **Shadow**: `shadow-sm` (or `shadow` in DataUser)
- **Padding**: `p-6` (24px)
- **Bottom margin**: `mb-6`

### Layout Grid
**Variant A** (DataKasus, DataPasien - 3 columns):
```html
<div class="grid grid-cols-1 md:grid-cols-3 gap-4">
```

**Variant B** (DataRetensi - 5 columns):
```html
<div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-4">
```

**Variant C** (DataUser - Flex layout):
```html
<div class="flex flex-col md:flex-row gap-4">
```

### Search/Filter Inputs
```html
<input
  v-model="searchText"
  type="text"
  placeholder="Placeholder text..."
  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
  @input|@change="handler"
/>
```

**Tailwind Classes Breakdown**:
- **Width**: `w-full`
- **Padding**: `px-4 py-2`
- **Border**: `border border-gray-300`
- **Radius**: `rounded-lg`
- **Focus state**: `focus:outline-none focus:ring-2 focus:ring-blue-500`

### Select Dropdowns
```html
<select
  v-model="filterStatus"
  class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
  @change="applyFilters"
>
```
Same styling as inputs, responsive width varies per layout.

### Search Icon Pattern (DataKasus, DataPasien)
```html
<div class="relative">
  <input type="text" placeholder="..." class="w-full px-4 py-2 border border-gray-300 rounded-lg..." />
  <svg class="absolute right-3 top-3 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <!-- SVG icon content -->
  </svg>
</div>
```

### Action Buttons (Add/Create)
```html
<button
  @click="openFormModal()"
  class="mt-4 bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-semibold flex items-center gap-2 w-full md:w-auto justify-center md:justify-start"
>
  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <!-- Plus icon -->
  </svg>
  Tambah [Item]
</button>
```

**Button Classes**:
- **Background**: `bg-blue-600`
- **Hover**: `hover:bg-blue-700`
- **Text**: `text-white px-6 py-2`
- **Radius**: `rounded-lg`
- **Font**: `font-semibold`
- **Layout**: `flex items-center gap-2`
- **Responsive**: `w-full md:w-auto justify-center md:justify-start`

---

## 3. TABLE CONTAINER

### Outer Wrapper
```html
<div class="bg-white rounded-lg shadow-sm overflow-hidden">
  <div class="overflow-x-auto">
    <table class="w-full">
```

**Classes**:
- **Background**: `bg-white`
- **Radius**: `rounded-lg`
- **Shadow**: `shadow-sm`
- **Overflow**: `overflow-hidden` (clip content to radius)
- **Scroll**: `overflow-x-auto` (enable horizontal scroll on mobile)

---

## 4. TABLE HEADER

### Styling Variants

**Variant A** (DataKasus, DataRetensi, DataPasien - Primary Blue):
```html
<thead class="bg-blue-600 text-white">
  <tr>
    <th class="px-6 py-4 text-left text-sm font-semibold">Header Text</th>
```

**Variant B** (DataUser - Secondary Gray):
```html
<thead class="bg-gray-100 border-b border-gray-300">
  <tr>
    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Header Text</th>
```

### Header Cell Properties
- **Padding**: `px-6 py-4` or `px-6 py-3`
- **Text alignment**: `text-left` (default) or `text-center` (action columns)
- **Text size**: `text-sm`
- **Font weight**: `font-semibold`
- **Colors**: 
  - Blue variant: white text
  - Gray variant: `text-gray-700`

---

## 5. TABLE BODY

### Row Structure
```html
<tbody>
  <tr v-for="item in items" :key="item.id" class="border-t border-gray-200 hover:bg-gray-50">
    <td class="px-6 py-4 text-sm text-gray-700">{{ data }}</td>
```

**Row Classes**:
- **Border**: `border-t border-gray-200`
- **Hover**: `hover:bg-gray-50`

### Cell Structure
- **Padding**: `px-6 py-4`
- **Text size**: `text-sm`
- **Default color**: `text-gray-700`
- **Variations**:
  - Important data: `text-sm font-medium text-gray-900`
  - Primary ID: `text-sm font-medium text-blue-600` (DataUser)
  - Primary ID: `text-sm font-semibold` (DataKasus, DataRetensi)

### Empty State Row
```html
<tr v-if="items.length === 0" class="border-t border-gray-200">
  <td colspan="8" class="px-6 py-8 text-center text-gray-500">
    Tidak ada data [item name]
  </td>
</tr>
```

**Empty State Classes**:
- **Padding**: `px-6 py-8` (or `py-12`)
- **Alignment**: `text-center`
- **Color**: `text-gray-500`
- **Colspan**: Matches total columns

---

## 6. STATUS BADGES

### Badge Base Structure
```html
<span
  :class="[
    'px-3 py-1 rounded-full text-xs font-semibold',
    condition ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'
  ]"
>
  {{ status }}
</span>
```

### Color Schemes

| Status | Classes |
|--------|---------|
| **Active** | `bg-green-100 text-green-800` |
| **Inactive** | `bg-gray-100 text-gray-800` |
| **Inactive (Alt)** | `bg-yellow-100 text-yellow-800` |
| **Delete/Musnah** | `bg-red-100 text-red-800` |
| **Admin Role** | `bg-blue-100 text-blue-800` |
| **Staff Role** | `bg-gray-100 text-gray-800` |

### Badge Typography
- **Padding**: `px-3 py-1`
- **Radius**: `rounded-full`
- **Text size**: `text-xs`
- **Font weight**: `font-semibold`
- **Display**: `inline-block` (implicit in span)

---

## 7. ACTION BUTTONS

### Button Style Variant A (DataKasus, DataRetensi, DataPasien)
Icon-only buttons with hover text color change:
```html
<td class="px-6 py-4 text-sm text-center">
  <div class="flex gap-2 justify-center">
    <button @click="action" class="text-blue-600 hover:text-blue-800 p-1" title="Edit">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <!-- Icon -->
      </svg>
    </button>
    <button @click="delete" class="text-red-600 hover:text-red-800 p-1" title="Hapus">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <!-- Icon -->
      </svg>
    </button>
  </div>
</td>
```

**Button Classes**:
- **Edit**: `text-blue-600 hover:text-blue-800 p-1`
- **Delete**: `text-red-600 hover:text-red-800 p-1`
- **Icon size**: `w-5 h-5`
- **Layout**: `flex gap-2 justify-center`

### Button Style Variant B (DataUser)
Boxed buttons with border:
```html
<td class="px-6 py-4 text-center">
  <div class="flex gap-2 justify-center">
    <button @click="edit" class="inline-flex items-center justify-center w-9 h-9 border border-blue-300 text-blue-600 rounded hover:bg-blue-50" title="Edit">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <!-- Icon -->
      </svg>
    </button>
    <button @click="delete" class="inline-flex items-center justify-center w-9 h-9 border border-red-300 text-red-600 rounded hover:bg-red-50" title="Hapus">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <!-- Icon -->
      </svg>
    </button>
  </div>
</td>
```

**Button Classes**:
- **Edit**: `inline-flex items-center justify-center w-9 h-9 border border-blue-300 text-blue-600 rounded hover:bg-blue-50`
- **Delete**: `inline-flex items-center justify-center w-9 h-9 border border-red-300 text-red-600 rounded hover:bg-red-50`
- **Icon size**: `w-4 h-4` (smaller than variant A)
- **Layout**: `flex gap-2 justify-center` in parent
- **Button size**: `w-9 h-9` (square, 36x36px)

### SVG Icon Properties
```html
<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="..." />
</svg>
```

**SVG Styles**:
- **Size**: `w-5 h-5` or `w-4 h-4`
- **Filled**: `fill="none"`
- **Stroke**: `stroke="currentColor"`
- **Stroke width**: `stroke-width="2"`
- **Stroke linecap**: `stroke-linecap="round"`
- **Stroke linejoin**: `stroke-linejoin="round"`

---

## 8. PAGINATION

### Container
```html
<div class="bg-gray-50 px-6 py-4 flex items-center justify-between border-t border-gray-200">
```

**Container Classes**:
- **Background**: `bg-gray-50`
- **Padding**: `px-6 py-4`
- **Layout**: `flex items-center justify-between`
- **Border**: `border-t border-gray-200`

### Information Text
```html
<div class="text-sm text-gray-600">
  Menampilkan 1 sampai {{ current }} dari {{ total }} hasil
</div>
```

**Text Classes**:
- **Size**: `text-sm`
- **Color**: `text-gray-600`

### Pagination Controls
```html
<div class="flex gap-2">
  <button
    @click="prevPage"
    :disabled="currentPage === 1"
    class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium disabled:opacity-50 disabled:cursor-not-allowed hover:bg-gray-50"
  >
    Previous
  </button>

  <div class="flex items-center gap-1">
    <button
      v-for="page in pages"
      :key="page"
      @click="goToPage(page)"
      :class="[
        'px-3 py-2 rounded-lg text-sm font-medium',
        currentPage === page
          ? 'bg-blue-600 text-white'
          : 'border border-gray-300 hover:bg-gray-50'
      ]"
    >
      {{ page }}
    </button>
  </div>

  <button
    @click="nextPage"
    :disabled="currentPage >= totalPages"
    class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium disabled:opacity-50 disabled:cursor-not-allowed hover:bg-gray-50"
  >
    Next
  </button>
</div>
```

**Button Classes**:
- **Regular button**: `px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium`
- **Hover**: `hover:bg-gray-50`
- **Disabled**: `disabled:opacity-50 disabled:cursor-not-allowed`
- **Active page**: `bg-blue-600 text-white`
- **Inactive page**: `border border-gray-300 hover:bg-gray-50`
- **Page number button**: `px-3 py-2` (slightly smaller)
- **Gap**: `gap-2` (between prev/next), `gap-1` (between page numbers)

---

## 9. SPACING & MARGINS REFERENCE

### Container & Section Spacing
| Element | Class | Value |
|---------|-------|-------|
| Page container | `p-8` | 32px |
| Header section | `mb-8` | margin-bottom: 32px |
| Action bar | `mb-6` | margin-bottom: 24px |
| Subtitle | `mt-2` | margin-top: 8px |
| Button (below filter) | `mt-4` | margin-top: 16px |
| Pagination | `mt-6` | margin-top: 24px |

### Internal Padding
| Element | Class | Value |
|---------|-------|-------|
| Action bar | `p-6` | 24px all sides |
| Table cell | `px-6 py-4` | x: 24px, y: 16px |
| Table header | `px-6 py-4/py-3` | x: 24px, y: 16px/12px |
| Pagination | `px-6 py-4` | x: 24px, y: 16px |
| Status badge | `px-3 py-1` | x: 12px, y: 4px |

### Element Gaps
| Element | Class | Value |
|---------|-------|-------|
| Form grid | `gap-4` | 16px between items |
| Flex layouts | `gap-4` | 16px between items |
| Buttons in row | `gap-2` | 8px between buttons |
| Pagination controls | `gap-2` | 8px between buttons |

---

## 10. TYPOGRAPHY REFERENCE

### Headings & Labels
| Level | Classes | Example |
|-------|---------|---------|
| Page title | `text-3xl font-bold text-gray-900` | "Data Kasus" |
| Subtitle | `text-gray-600 mt-2` | "Master data kasus..." |
| Table header | `text-sm font-semibold` | Column names |
| Badge | `text-xs font-semibold` | Status text |

### Body Text
| Type | Classes | Usage |
|------|---------|-------|
| Regular data | `text-sm text-gray-700` | Most cells |
| Important data | `text-sm font-medium text-gray-900` | Names, IDs |
| Muted | `text-sm text-gray-600` | Secondary info |
| Placeholder/Empty | `text-gray-500` | Empty states |
| Light/Disabled | `text-gray-400` | Icons, disabled text |

---

## 11. COLOR PALETTE

### Primary Colors
- **Primary Blue**: `blue-600` (actions, headers - DataKasus/Retensi/Pasien)
- **Blue Light**: `blue-100` (badges - admin roles)
- **Blue Hover**: `blue-700` (button hover)
- **Blue Ring**: `blue-500` (focus state)

### Status Colors
- **Success/Active**: `green-100` (bg), `green-800` (text)
- **Warning/Inactive**: `yellow-100` (bg), `yellow-800` (text)
- **Neutral/Inactive**: `gray-100` (bg), `gray-800` (text)
- **Danger/Delete**: `red-100` (bg), `red-800` (text)
- **Danger Hover**: `red-50` (hover state)

### Gray Scale
- **Page background**: `gray-50` (lightest)
- **Component background**: `white` (cards)
- **Header alt**: `gray-100` (secondary)
- **Borders**: `gray-200` (light border)
- **Secondary header**: `gray-300` (darker border)
- **Hover**: `gray-50` (subtle highlight)
- **Text primary**: `gray-900` (darkest)
- **Text secondary**: `gray-700` (dark)
- **Text muted**: `gray-600` (medium)
- **Text light**: `gray-500` (light)
- **Icon**: `gray-400` (lighter)

---

## 12. RESPONSIVE DESIGN

### Breakpoints Used
- **Mobile**: Default (no prefix)
- **Medium+**: `md:` prefix

### Responsive Examples
```html
<!-- Grid layout -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-4">

<!-- Flex layout -->
<div class="flex flex-col md:flex-row gap-4">

<!-- Width -->
<button class="w-full md:w-auto">

<!-- Justify -->
<button class="justify-center md:justify-start">

<!-- Display -->
<div class="md:w-40">
```

---

## 13. COMPLETE COMPONENT STRUCTURE TEMPLATE

### Full Data Page Pattern
```html
<div class="min-h-screen bg-gray-50 p-8">
  <!-- Header -->
  <div class="mb-8">
    <h1 class="text-3xl font-bold text-gray-900">Page Title</h1>
    <p class="text-gray-600 mt-2">Subtitle</p>
  </div>

  <!-- Action Bar -->
  <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
      <!-- Inputs/Selects -->
    </div>
    <button class="mt-4 bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-semibold flex items-center gap-2 w-full md:w-auto justify-center md:justify-start">
      + Add Item
    </button>
  </div>

  <!-- Table -->
  <div class="bg-white rounded-lg shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
      <table class="w-full">
        <thead class="bg-blue-600 text-white">
          <tr>
            <th class="px-6 py-4 text-left text-sm font-semibold">Header</th>
            <th class="px-6 py-4 text-center text-sm font-semibold">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-if="items.length === 0" class="border-t border-gray-200">
            <td colspan="2" class="px-6 py-8 text-center text-gray-500">
              No data
            </td>
          </tr>
          <tr v-for="item in items" :key="item.id" class="border-t border-gray-200 hover:bg-gray-50">
            <td class="px-6 py-4 text-sm text-gray-700">{{ item.name }}</td>
            <td class="px-6 py-4 text-sm text-center">
              <div class="flex gap-2 justify-center">
                <button class="text-blue-600 hover:text-blue-800 p-1">
                  <!-- Edit icon -->
                </button>
                <button class="text-red-600 hover:text-red-800 p-1">
                  <!-- Delete icon -->
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <div class="bg-gray-50 px-6 py-4 flex items-center justify-between border-t border-gray-200">
      <div class="text-sm text-gray-600">
        Showing 1 to 10 of 100 results
      </div>
      <div class="flex gap-2">
        <button class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium hover:bg-gray-50">
          Previous
        </button>
        <button class="px-3 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium">
          1
        </button>
        <button class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium hover:bg-gray-50">
          Next
        </button>
      </div>
    </div>
  </div>
</div>
```

---

## 14. KEY CONSISTENCY RULES TO FOLLOW

1. ✅ All primary CTAs use **blue-600** with **blue-700** hover
2. ✅ All data tables use **shadow-sm** and **rounded-lg** wrapping
3. ✅ All table headers use **blue-600** (primary style) or **gray-100** (secondary)
4. ✅ All table rows have **border-t border-gray-200** and **hover:bg-gray-50**
5. ✅ All cells use **px-6 py-4** padding (consistency crucial)
6. ✅ All status badges use **rounded-full** with color pairs (100/800)
7. ✅ All inputs use **blue-500 focus ring** (2px ring)
8. ✅ All pagination uses **bg-gray-50** container with **border-t border-gray-200**
9. ✅ All pages use **bg-gray-50 p-8** for main container
10. ✅ All action buttons follow either Variant A (icon-only) or Variant B (boxed)

---

## 15. PAGES ANALYZED

| File | Header Style | Button Style | Notes |
|------|--------------|--------------|-------|
| **DataKasus.vue** | Blue-600 | Icon-only (Variant A) | 3-column filter grid |
| **DataUser.vue** | Gray-100 | Boxed (Variant B) | Flex layout, 10 per page |
| **DataRetensi.vue** | Blue-600 | Icon-only (Variant A) | 5-column filter grid, hitung ulang button |
| **DataPasien.vue** | Blue-600 | Icon-only (Variant A) | 3-column filter grid |

---

## Summary
This analysis provides a complete reference for maintaining consistent table styling across all Vue data pages. All components follow a cohesive design system based on Tailwind CSS utility classes with carefully chosen color palette, spacing, and typography.
