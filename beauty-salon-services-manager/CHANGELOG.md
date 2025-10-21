# Changelog

All notable changes to the Beauty Salon Services Manager plugin will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.0.0] - 2025-10-21

### Added
- Initial release of Beauty Salon Services Manager plugin
- Custom post type `bslm_service` for managing beauty services
- Custom taxonomy `bslm_service_group` for categorizing services
- Service detail meta boxes (Time Duration, Price, Additional Notes)
- Full Elementor integration with custom widget "Beauty Services Grid"
- Comprehensive widget controls:
  - Service Selection (All, By Group, Manual)
  - Display Options (Image, Title, Description, Time, Price, Button)
  - Layout Controls (Responsive Columns, Gaps)
  - Complete Styling Options for all elements
- Admin interface with custom columns (Thumbnail, Groups, Time, Price)
- Frontend responsive grid layout
- Mobile-first CSS with breakpoints for tablet and mobile
- Admin CSS for improved meta box styling
- JavaScript for enhanced admin and frontend functionality
- Template system with `service-card.php` and `single-service.php`
- Extensibility hooks and filters:
  - `bslm_before_service_content` action hook
  - `bslm_after_service_content` action hook
  - `bslm_service_meta_display` action hook
  - `bslm_service_query_args` filter hook
  - `bslm_service_card_classes` filter hook
  - `bslm_service_excerpt_length` filter hook
  - `bslm_service_price_format` filter hook
  - `bslm_service_button_text` filter hook
- Plugin activation/deactivation handlers
- Flush rewrite rules on activation
- REST API support for services and service groups
- Translation ready with text domain `beauty-salon-services-manager`
- RTL (Right-to-Left) language support
- Documentation files:
  - README.md - Main documentation
  - INSTALL.md - Installation guide
  - CHANGELOG.md - Version history
- Admin menu with custom icon (dashicons-heart)
- Featured image support for services
- Quick edit support for service meta fields
- Lazy loading for service images
- Scroll animations for service cards
- Performance optimizations:
  - Cached queries
  - Conditional script/style loading
  - Optimized database queries
- Security features:
  - Nonce verification
  - Data sanitization
  - Capability checks
- Accessibility features:
  - Screen reader text
  - ARIA labels
  - Keyboard navigation support
  - Focus states

### Features in Detail

#### Custom Post Type
- Title support
- Editor support (WYSIWYG)
- Featured image support
- Custom fields support
- Hierarchical: No
- Public: Yes
- Show in REST: Yes
- Menu position: 20

#### Service Groups Taxonomy
- Hierarchical: Yes
- Public: Yes
- Show in admin column: Yes
- Show in REST: Yes
- Custom slug: 'service-group'

#### Elementor Widget Controls
**Content Tab:**
- Query Type selector
- Service Groups multi-select
- Manual Services multi-select
- Posts per page number field
- Order by dropdown
- Order direction dropdown
- Show/Hide toggles for all elements
- Description length control
- Button text customization

**Style Tab:**
- Responsive column controls
- Column and row gap sliders
- Card background color
- Border controls (type, width, color, radius)
- Box shadow controls
- Padding dimensions
- Image height slider
- Image object-fit selector
- Image border radius
- Title typography controls
- Title color picker
- Title alignment options
- Title margin controls
- Description typography controls
- Description color picker
- Description alignment options
- Meta typography controls
- Meta color pickers
- Meta icon toggle and color
- Button typography controls
- Button normal/hover states
- Button border controls
- Button border radius
- Button padding controls

#### Template System
- Override support in theme
- Template location: `yourtheme/beauty-salon-services-manager/`
- Service card template
- Single service template
- Archive template support (planned)

#### Admin Features
- Custom admin columns
- Thumbnail column with image preview
- Service Groups column
- Time column
- Price column
- Quick edit integration
- Bulk actions support
- Filter by service group
- Search services

### File Structure
```
beauty-salon-services-manager/
├── assets/
│   ├── css/
│   │   ├── admin.css
│   │   └── frontend.css
│   └── js/
│       ├── admin.js
│       └── frontend.js
├── includes/
│   ├── widgets/
│   │   └── class-beauty-services-grid-widget.php
│   ├── class-activator.php
│   ├── class-beauty-salon-services-manager.php
│   ├── class-deactivator.php
│   ├── class-elementor-widget.php
│   ├── class-loader.php
│   ├── class-meta-boxes.php
│   ├── class-post-types.php
│   └── class-taxonomies.php
├── templates/
│   ├── service-card.php
│   └── single-service.php
├── beauty-salon-services-manager.php
├── CHANGELOG.md
├── INSTALL.md
└── README.md
```

### Known Issues
- None reported

### Compatibility
- WordPress 6.0+
- PHP 7.4+
- Elementor 3.0+
- MySQL 5.6+ or MariaDB 10.0+

---

## [Unreleased]

### Planned Features for Future Releases

#### Version 1.1.0 (Planned)
- [ ] Service booking system integration
- [ ] Service availability calendar
- [ ] Staff assignment to services
- [ ] Service duration as time picker
- [ ] Price variations (e.g., different prices for different durations)
- [ ] Service packages/bundles
- [ ] Discount and promotion support
- [ ] Service comparison feature

#### Version 1.2.0 (Planned)
- [ ] Client testimonials per service
- [ ] Service ratings and reviews
- [ ] Gallery support for multiple service images
- [ ] Video support for service demonstrations
- [ ] Before/after image galleries
- [ ] Service FAQ accordion
- [ ] Related services display

#### Version 1.3.0 (Planned)
- [ ] Frontend service submission form
- [ ] Service search and filtering widget
- [ ] Advanced search with AJAX
- [ ] Service wishlist/favorites
- [ ] Print service list functionality
- [ ] Export services to PDF
- [ ] Email service details

#### Version 2.0.0 (Planned)
- [ ] Complete booking and appointment system
- [ ] Payment gateway integration
- [ ] Staff management system
- [ ] Client area with booking history
- [ ] Email notifications
- [ ] SMS notifications integration
- [ ] Google Calendar sync
- [ ] Analytics dashboard

### Ideas Under Consideration
- Integration with popular appointment booking plugins
- WooCommerce integration for selling service packages
- Membership system for recurring services
- Gift card and voucher system
- Mobile app companion
- Multi-location support
- Social media sharing buttons
- SEO optimization features
- Schema markup for services
- Import/export functionality

---

## Version History

| Version | Release Date | Major Changes |
|---------|--------------|---------------|
| 1.0.0   | 2025-10-21  | Initial release |

---

## Upgrade Notices

### 1.0.0
Initial release. No upgrade necessary.

---

## Contributing

We welcome contributions! Here's how the changelog should be updated:

### Categories for Changes
- **Added**: New features
- **Changed**: Changes in existing functionality
- **Deprecated**: Soon-to-be removed features
- **Removed**: Removed features
- **Fixed**: Bug fixes
- **Security**: Security fixes

### Adding to Changelog
When contributing, please update the [Unreleased] section with your changes under the appropriate category.

---

## Support

For questions about changes in any version:
- Review the [README.md](README.md) for current features
- Check [INSTALL.md](INSTALL.md) for installation guidance
- Open an issue on GitHub for version-specific questions

---

**Note**: This changelog follows [Keep a Changelog](https://keepachangelog.com/) format and [Semantic Versioning](https://semver.org/) principles.
