# PrEP Eligibility Quiz WordPress Plugin

A WordPress plugin that converts the PrEP Eligibility Quiz into a shortcode-enabled component for WordPress sites.

## Installation

1. **Upload the plugin folder** to your WordPress site:
   - Navigate to `/wp-content/plugins/`
   - Upload the `prep-quiz` folder

2. **Activate the plugin** from WordPress Admin:
   - Go to **Plugins** in your WordPress dashboard
   - Find "PrEP Eligibility Quiz"
   - Click **Activate**

## Usage

Add the quiz to any page or post using the shortcode:

```
[prep_quiz]
```

## Features

- ✅ All original logic and styling preserved
- ✅ 8-question interactive quiz
- ✅ Keyboard navigation support (A/B/C keys, Enter)
- ✅ Smooth animations and transitions
- ✅ Responsive design (mobile-friendly)
- ✅ No tracking, cookies, or external data transmission
- ✅ Accessibility compliant (ARIA labels)

## File Structure

```
prep-quiz/
├── prep-quiz.php           # Main plugin file
├── assets/
│   ├── css/
│   │   └── prep-quiz.css   # All styling
│   └── js/
│       └── prep-quiz.js    # All quiz logic
└── README.md               # This file
```

## Customization

### Changing Colors

Edit the CSS variables in `assets/css/prep-quiz.css`:

```css
:root {
  --bg: #942020; /* Main background color */
  --white: #ffffff; /* Text color */
  /* ... other colors ... */
}
```

### Modifying Questions or Results

Edit the data arrays in `assets/js/prep-quiz.js`:

- `QUESTIONS` array - contains all 8 questions
- `RESULTS` array - contains the two possible result screens

## License

GPL v2 or later
