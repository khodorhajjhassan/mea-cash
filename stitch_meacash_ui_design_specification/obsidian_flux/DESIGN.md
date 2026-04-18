```markdown
# Design System Specification: The Kinetic Noir

## 1. Overview & Creative North Star
### Creative North Star: "The Kinetic Noir"
This design system is a study in high-contrast cinematic tension. It moves beyond the static "dark mode" by treating the interface as a living, breathing digital atmosphere. By blending the deep, obsidian foundations of "Noir" with the hyper-vibrant energy of electric gradients, we create a "Kinetic" experience that feels both premium and experimental.

**Breaking the Template:**
To achieve a "super modern" look, designers must abandon the traditional box-model grid. Embrace **intentional asymmetry**: allow large display typography to bleed off-canvas or overlap glass containers. Use the vibrancy of the primary and secondary colors not just for buttons, but as "light leaks" that glow from behind surfaces, creating a sense of three-dimensional depth and digital soul.

---

## 2. Colors & Atmospheric Depth

### The Tonal Palette
The palette is rooted in `surface` (#111319), but its energy comes from the interplay between the `primary` cyan/blue and `secondary` magenta tones.

*   **Primary (Electric Blue/Cyan):** Use `primary_container` (#00f0ff) for high-action triggers.
*   **Secondary (Magenta):** Use `secondary_container` (#fe00fe) for accent moments and creative highlights.
*   **Tertiary (Deep Violet):** Use `on_tertiary_container` (#7213ff) to bridge the gap between blue and magenta.

### The "No-Line" Rule
**Prohibit 1px solid borders for sectioning.** Boundaries must be defined through:
1.  **Tonal Shifts:** Place a `surface_container_low` section against a `surface` background.
2.  **Luminance Contrast:** Using a subtle glow from a `primary` gradient to define a container's edge.
3.  **Negative Space:** Using the Spacing Scale to create "breathing rooms" that act as invisible dividers.

### Surface Hierarchy & Nesting
Treat the UI as a series of physical layers. 
*   **Base:** `surface` (#111319)
*   **Level 1 (Sections):** `surface_container_low` (#191b22)
*   **Level 2 (Cards):** `surface_container_high` (#282a30)
*   **Level 3 (Floating Elements):** `surface_container_highest` (#33343b) with 60% opacity and a 20px backdrop-blur.

### The "Glass & Gradient" Rule
Flat colors are forbidden for hero elements. Main CTAs and interactive cards must utilize a **Linear Gradient (45°)**:
*   **Start:** `primary_fixed` (#7df4ff)
*   **End:** `secondary_fixed_dim` (#ffabf3)
*   **Polish:** Apply a subtle `on_surface` inner-glow (1px, 10% opacity) to the top edge of glass containers to simulate light catching on a glass edge.

---

## 3. Typography
The typography strategy relies on the tension between the technical precision of **Space Grotesk** and the clean readability of **Inter**.

*   **Display & Headlines (Space Grotesk):** These are your "vibe setters." Use `display-lg` (3.5rem) with tight letter-spacing (-0.02em) for hero sections. Headlines should feel authoritative and "editorial"—don't be afraid to use `on_tertiary_container` for specific words in a headline to create visual rhythm.
*   **Title & Body (Inter):** For information density. `body-lg` (1rem) is the workhorse. Ensure `title-lg` uses a slightly heavier weight to maintain hierarchy against the aggressive Headlines.
*   **Labels (Space Grotesk):** All labels (`label-md`) should be in Space Grotesk, Uppercase, with +0.1em tracking to evoke a "monospace/tech" aesthetic that fits the gaming noir theme.

---

## 4. Elevation & Depth

### The Layering Principle
Depth is achieved by "stacking" the surface tiers. Instead of a shadow, a `surface_container_highest` card sitting on a `surface_container_low` section provides enough tonal delta to feel elevated.

### Ambient Shadows
When an element must "float" (e.g., a modal or a primary button):
*   **Blur:** 40px – 60px.
*   **Opacity:** 8%.
*   **Color:** Use the `primary` or `secondary` token color rather than black. This creates a "neon glow" rather than a heavy shadow.

### The "Ghost Border" Fallback
If a boundary is required for accessibility, use a **Ghost Border**:
*   **Stroke:** `outline_variant` (#3b494b) at 15% opacity.
*   **Effect:** This maintains the "no-line" aesthetic while providing just enough definition for low-vision users.

---

## 5. Components

### Buttons
*   **Primary:** Gradient fill (`primary` to `secondary`). `xl` (1.5rem) roundedness. No border. High-contrast `on_primary_fixed` text.
*   **Secondary:** Glassmorphism. `surface_container_highest` at 40% opacity, 12px backdrop-blur, and a `Ghost Border`.
*   **Tertiary:** Text-only, Space Grotesk, Uppercase, with a 2px underline that appears on hover using the `primary_container` color.

### Cards & Lists
*   **The Forbid Rule:** No horizontal rules (dividers). 
*   **Separation:** Use a `surface_container_low` background for the even items in a list, or simply 32px of vertical whitespace.
*   **Hover State:** Cards should subtly scale (1.02x) and increase their background opacity from 40% to 60%.

### Input Fields
*   **Container:** `surface_container_lowest` (#0c0e14).
*   **Active State:** The bottom edge glows with a 2px `primary_container` gradient. Helper text appears in `label-sm` Space Grotesk.

### Floating Action Glows (New Component)
*   **Concept:** Unstructured decorative blurs.
*   **Usage:** Place a 200px circle of `secondary_container` with a 150px Gaussian blur behind key content areas to "lift" the noir background and prevent the UI from feeling "dead."

---

## 6. Do's and Don'ts

### Do:
*   **Do** use asymmetrical layouts. Place a headline on the left and the body copy shifted 2 columns to the right.
*   **Do** use vibrant gradients as focal points. 
*   **Do** lean into the "Glass" look for any overlay or navigation bar.
*   **Do** ensure all interactive elements have a focus state using the `primary` glow.

### Don't:
*   **Don't** use 100% opaque borders. It breaks the "Kinetic Noir" immersion.
*   **Don't** use standard "drop shadows" (black, high opacity).
*   **Don't** use the `error` color for anything other than critical failures. Use `secondary` (Magenta) for attention-grabbing moments that aren't errors.
*   **Don't** clutter the screen. If a section feels heavy, increase the spacing rather than adding a divider.

---
*Note to Designers: This system is about the balance of light and shadow. If it feels too bright, increase the surface-container-low areas. If it feels too dark, add a "Light Leak" gradient blur.*```