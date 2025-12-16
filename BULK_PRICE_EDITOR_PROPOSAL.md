# Bulk Price Editor Tool - Feature Proposal
## Beauty Salon Services Manager Plugin

**Date:** 2025-12-16
**Proposed Version:** v1.5.0
**Development Priority:** HIGH ⭐

---

## Feature Overview

A **Bulk Price Editor** admin page that displays all services in a table/list format with inline editing capabilities, allowing salon owners to update multiple service prices efficiently without navigating to individual service edit pages.

---

## User Interface Mockup

### Layout Design

```
┌──────────────────────────────────────────────────────────────────────────┐
│ Beauty Salon Services Manager > Bulk Price Editor                       │
├──────────────────────────────────────────────────────────────────────────┤
│                                                                           │
│  [Search services...] [Filter by Category ▼] [Filter by Tag ▼]          │
│                                                                           │
│  [Apply % Increase: [_10_]% to selected] [Apply Fixed Amount: [_5_]€]   │
│                                                                           │
│  ┌────────────────────────────────────────────────────────────────────┐  │
│  │ ☐  Service Name          | Category      | Current Price | Actions │  │
│  ├────────────────────────────────────────────────────────────────────┤  │
│  │ ☑  Facial Treatment      | Facial        | 50€           | [Edit]  │  │
│  │    └─ 1 seance(s)        |              | 50€            |         │  │
│  │    └─ 5 seance(s)        |              | 225€           |         │  │
│  │    └─ 10 seance(s)       |              | 400€           |         │  │
│  │                                                                      │  │
│  │ ☑  Laser Hair Removal    | Laser         | 80€           | [Edit]  │  │
│  │    └─ 1 seance(s)        |              | 80€            |         │  │
│  │    └─ 6 seance(s)        |              | 450€           |         │  │
│  │                                                                      │  │
│  │ ☐  Massage Therapy       | Massage       | 60€           | [Edit]  │  │
│  │    └─ 1 seance(s)        |              | 60€            |         │  │
│  └────────────────────────────────────────────────────────────────────┘  │
│                                                                           │
│  [Save All Changes]  [Reset]  [Export to CSV]  [Import from CSV]        │
│                                                                           │
└──────────────────────────────────────────────────────────────────────────┘
```

### Inline Editing View

When user clicks "Edit" on a service:

```
┌────────────────────────────────────────────────────────────────────────┐
│ ☑  Facial Treatment      | Facial        |               | [Save] [X] │
│    ┌──────────────────────────────────────────────────────────────┐   │
│    │ Sessions    | Regular Price  | Member Price  | Valid From   │   │
│    ├──────────────────────────────────────────────────────────────┤   │
│    │ 1 seance(s) | [__50€__]     | [__45€__]    | [2025-01-01] │   │
│    │ 5 seance(s) | [__225€__]    | [__200€__]   | [2025-01-01] │   │
│    │ 10 seance(s)| [__400€__]    | [__360€__]   | [2025-01-01] │   │
│    │             | [+ Add Price Tier]                            │   │
│    └──────────────────────────────────────────────────────────────┘   │
└────────────────────────────────────────────────────────────────────────┘
```

---

## Key Features

### 1. **List View with All Services**
- Display all services in expandable rows
- Show service name, category, and base price
- Expand to show all price tiers (packages)
- Checkbox selection for bulk operations

### 2. **Inline Editing**
- Click "Edit" to expand inline editing form
- Update prices without page reload (AJAX)
- Real-time validation
- Auto-save on blur (optional)

### 3. **Bulk Operations**
- **Percentage Increase:** Apply X% increase to selected services
  - Example: Increase all selected services by 10%
- **Fixed Amount:** Add/subtract fixed amount
  - Example: Add 5€ to all selected services
- **Bulk Selection:** Select all, select by category, select by tag

### 4. **Filtering & Search**
- Search by service name
- Filter by service category
- Filter by service tag
- Filter by price range

### 5. **Import/Export**
- **Export to CSV:** Download all prices for Excel editing
- **Import from CSV:** Upload modified prices
- Template CSV with proper headers

### 6. **Price History (Optional)**
- Track who changed what and when
- Rollback capability
- Audit log for compliance

---

## Technical Implementation

### File Structure

```
includes/
├── admin/
│   ├── class-bulk-price-editor.php       // Main admin page class
│   ├── class-bulk-price-ajax.php         // AJAX handlers
│   └── views/
│       ├── bulk-price-editor.php         // Main view template
│       └── partials/
│           ├── price-row.php             // Single service row
│           └── inline-editor.php         // Inline edit form
├── class-price-csv-handler.php           // Import/export CSV
└── class-price-change-logger.php         // Price history tracking

assets/
├── css/
│   └── bulk-price-editor.css             // Styling for editor
└── js/
    └── bulk-price-editor.js              // Frontend JS logic
```

### Database Schema (Optional - for history)

```sql
CREATE TABLE wp_bslm_price_changes (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    service_id BIGINT UNSIGNED NOT NULL,
    old_prices LONGTEXT NOT NULL,          -- JSON of old price_options
    new_prices LONGTEXT NOT NULL,          -- JSON of new price_options
    changed_by BIGINT UNSIGNED NOT NULL,   -- User ID
    change_type VARCHAR(50) NOT NULL,      -- 'manual', 'bulk_percent', 'bulk_fixed', 'import'
    change_note TEXT,                      -- Optional note
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_service_id (service_id),
    INDEX idx_changed_by (changed_by),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### Core Class Structure

```php
/**
 * Bulk Price Editor Admin Page
 */
class BSLM_Bulk_Price_Editor {

    /**
     * Constructor
     */
    public function __construct() {
        add_action('admin_menu', array($this, 'add_admin_page'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_assets'));
    }

    /**
     * Add admin menu page
     */
    public function add_admin_page() {
        add_submenu_page(
            'edit.php?post_type=bslm_service',
            __('Bulk Price Editor', 'beauty-salon-services-manager'),
            __('Bulk Prices', 'beauty-salon-services-manager'),
            'edit_posts',  // Capability required
            'bslm-bulk-prices',
            array($this, 'render_page')
        );
    }

    /**
     * Render the bulk price editor page
     */
    public function render_page() {
        // Security check
        if (!current_user_can('edit_posts')) {
            wp_die(__('You do not have permission to access this page.'));
        }

        // Get all services
        $services = $this->get_all_services();

        // Load view template
        include BSLM_PLUGIN_DIR . 'includes/admin/views/bulk-price-editor.php';
    }

    /**
     * Get all services with price data
     */
    private function get_all_services() {
        $args = array(
            'post_type' => 'bslm_service',
            'posts_per_page' => -1,
            'post_status' => 'publish',
            'orderby' => 'title',
            'order' => 'ASC',
        );

        $services = get_posts($args);
        $services_data = array();

        foreach ($services as $service) {
            $price_options = get_post_meta($service->ID, '_bslm_service_price_options', true);
            $categories = wp_get_post_terms($service->ID, 'bslm_service_group');

            $services_data[] = array(
                'id' => $service->ID,
                'title' => $service->post_title,
                'price_options' => is_array($price_options) ? $price_options : array(),
                'categories' => $categories,
            );
        }

        return $services_data;
    }

    /**
     * Enqueue admin assets
     */
    public function enqueue_assets($hook) {
        // Only load on our page
        if ($hook !== 'bslm_service_page_bslm-bulk-prices') {
            return;
        }

        wp_enqueue_style(
            'bslm-bulk-price-editor',
            BSLM_PLUGIN_URL . 'assets/css/bulk-price-editor.css',
            array(),
            BSLM_VERSION
        );

        wp_enqueue_script(
            'bslm-bulk-price-editor',
            BSLM_PLUGIN_URL . 'assets/js/bulk-price-editor.js',
            array('jquery'),
            BSLM_VERSION,
            true
        );

        // Localize script
        wp_localize_script('bslm-bulk-price-editor', 'bslmBulkEditor', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('bslm_bulk_price_nonce'),
            'strings' => array(
                'saving' => __('Saving...', 'beauty-salon-services-manager'),
                'saved' => __('Saved!', 'beauty-salon-services-manager'),
                'error' => __('Error saving prices', 'beauty-salon-services-manager'),
                'confirm_bulk' => __('Apply changes to %d selected services?', 'beauty-salon-services-manager'),
            ),
        ));
    }
}
```

### AJAX Handler

```php
/**
 * AJAX Handlers for Bulk Price Editor
 */
class BSLM_Bulk_Price_Ajax {

    public function __construct() {
        add_action('wp_ajax_bslm_update_service_prices', array($this, 'update_service_prices'));
        add_action('wp_ajax_bslm_bulk_apply_percentage', array($this, 'bulk_apply_percentage'));
        add_action('wp_ajax_bslm_bulk_apply_fixed', array($this, 'bulk_apply_fixed'));
    }

    /**
     * Update single service prices
     */
    public function update_service_prices() {
        // Verify nonce
        check_ajax_referer('bslm_bulk_price_nonce', 'nonce');

        // Check permissions
        if (!current_user_can('edit_posts')) {
            wp_send_json_error(array('message' => 'Permission denied'));
        }

        // Get and validate data
        $service_id = isset($_POST['service_id']) ? absint($_POST['service_id']) : 0;
        $price_options = isset($_POST['price_options']) ? $_POST['price_options'] : array();

        if (!$service_id || !is_array($price_options)) {
            wp_send_json_error(array('message' => 'Invalid data'));
        }

        // Sanitize price options
        $sanitized_prices = array();
        foreach ($price_options as $option) {
            $sanitized_prices[] = array(
                'sessions' => absint($option['sessions']),
                'price' => sanitize_text_field($option['price']),
            );
        }

        // Save old prices for history
        $old_prices = get_post_meta($service_id, '_bslm_service_price_options', true);

        // Update prices
        update_post_meta($service_id, '_bslm_service_price_options', $sanitized_prices);

        // Log change
        $this->log_price_change($service_id, $old_prices, $sanitized_prices, 'manual');

        wp_send_json_success(array(
            'message' => 'Prices updated successfully',
            'service_id' => $service_id,
        ));
    }

    /**
     * Apply percentage increase to multiple services
     */
    public function bulk_apply_percentage() {
        check_ajax_referer('bslm_bulk_price_nonce', 'nonce');

        if (!current_user_can('edit_posts')) {
            wp_send_json_error(array('message' => 'Permission denied'));
        }

        $service_ids = isset($_POST['service_ids']) ? array_map('absint', $_POST['service_ids']) : array();
        $percentage = isset($_POST['percentage']) ? floatval($_POST['percentage']) : 0;

        if (empty($service_ids) || $percentage === 0) {
            wp_send_json_error(array('message' => 'Invalid data'));
        }

        $updated_count = 0;

        foreach ($service_ids as $service_id) {
            $price_options = get_post_meta($service_id, '_bslm_service_price_options', true);

            if (!is_array($price_options)) {
                continue;
            }

            $old_prices = $price_options;
            $new_prices = array();

            foreach ($price_options as $option) {
                // Extract numeric value from price string
                $price_numeric = floatval(preg_replace('/[^0-9.]/', '', $option['price']));

                // Apply percentage
                $new_price_numeric = $price_numeric * (1 + ($percentage / 100));

                // Extract currency symbol
                $currency = preg_replace('/[0-9.,\s]/', '', $option['price']);
                if (empty($currency)) {
                    $currency = '€';
                }

                // Format new price
                $new_price = number_format($new_price_numeric, 2, '.', '') . $currency;

                $new_prices[] = array(
                    'sessions' => $option['sessions'],
                    'price' => $new_price,
                );
            }

            // Update prices
            update_post_meta($service_id, '_bslm_service_price_options', $new_prices);

            // Log change
            $this->log_price_change(
                $service_id,
                $old_prices,
                $new_prices,
                'bulk_percent',
                sprintf('Applied %s%% increase', $percentage)
            );

            $updated_count++;
        }

        wp_send_json_success(array(
            'message' => sprintf('Updated %d services', $updated_count),
            'count' => $updated_count,
        ));
    }

    /**
     * Log price change for history
     */
    private function log_price_change($service_id, $old_prices, $new_prices, $type, $note = '') {
        global $wpdb;

        $wpdb->insert(
            $wpdb->prefix . 'bslm_price_changes',
            array(
                'service_id' => $service_id,
                'old_prices' => wp_json_encode($old_prices),
                'new_prices' => wp_json_encode($new_prices),
                'changed_by' => get_current_user_id(),
                'change_type' => $type,
                'change_note' => $note,
                'created_at' => current_time('mysql'),
            ),
            array('%d', '%s', '%s', '%d', '%s', '%s', '%s')
        );
    }
}
```

### JavaScript Implementation

```javascript
/**
 * Bulk Price Editor - Frontend Logic
 */
(function($) {
    'use strict';

    const BulkPriceEditor = {
        init: function() {
            this.bindEvents();
            this.initDataTable();
        },

        bindEvents: function() {
            // Edit button click
            $(document).on('click', '.bslm-edit-price', this.toggleEditMode);

            // Save button click
            $(document).on('click', '.bslm-save-prices', this.saveServicePrices);

            // Bulk percentage apply
            $('#bslm-apply-percentage').on('click', this.applyPercentage);

            // Bulk fixed amount apply
            $('#bslm-apply-fixed').on('click', this.applyFixed);

            // Select all checkbox
            $('#bslm-select-all').on('change', this.toggleSelectAll);

            // Export to CSV
            $('#bslm-export-csv').on('click', this.exportToCSV);
        },

        toggleEditMode: function(e) {
            e.preventDefault();
            const $row = $(this).closest('.bslm-service-row');
            $row.find('.bslm-price-view').toggle();
            $row.find('.bslm-price-edit').toggle();
        },

        saveServicePrices: function(e) {
            e.preventDefault();
            const $button = $(this);
            const $row = $button.closest('.bslm-service-row');
            const serviceId = $row.data('service-id');

            // Collect price options
            const priceOptions = [];
            $row.find('.bslm-price-option').each(function() {
                priceOptions.push({
                    sessions: $(this).find('.sessions-input').val(),
                    price: $(this).find('.price-input').val(),
                });
            });

            // Show loading state
            $button.prop('disabled', true).text(bslmBulkEditor.strings.saving);

            // AJAX save
            $.ajax({
                url: bslmBulkEditor.ajaxurl,
                type: 'POST',
                data: {
                    action: 'bslm_update_service_prices',
                    nonce: bslmBulkEditor.nonce,
                    service_id: serviceId,
                    price_options: priceOptions,
                },
                success: function(response) {
                    if (response.success) {
                        $button.text(bslmBulkEditor.strings.saved);
                        setTimeout(function() {
                            $button.prop('disabled', false).text('Save');
                            $row.find('.bslm-price-view').toggle();
                            $row.find('.bslm-price-edit').toggle();
                        }, 1000);
                    } else {
                        alert(bslmBulkEditor.strings.error);
                        $button.prop('disabled', false).text('Save');
                    }
                },
                error: function() {
                    alert(bslmBulkEditor.strings.error);
                    $button.prop('disabled', false).text('Save');
                }
            });
        },

        applyPercentage: function(e) {
            e.preventDefault();
            const percentage = parseFloat($('#percentage-value').val());
            const selectedIds = BulkPriceEditor.getSelectedServiceIds();

            if (!percentage || selectedIds.length === 0) {
                alert('Please enter a percentage and select services');
                return;
            }

            if (!confirm(sprintf(bslmBulkEditor.strings.confirm_bulk, selectedIds.length))) {
                return;
            }

            $.ajax({
                url: bslmBulkEditor.ajaxurl,
                type: 'POST',
                data: {
                    action: 'bslm_bulk_apply_percentage',
                    nonce: bslmBulkEditor.nonce,
                    service_ids: selectedIds,
                    percentage: percentage,
                },
                success: function(response) {
                    if (response.success) {
                        alert(response.data.message);
                        location.reload(); // Reload to show new prices
                    }
                }
            });
        },

        getSelectedServiceIds: function() {
            const ids = [];
            $('.bslm-service-checkbox:checked').each(function() {
                ids.push($(this).val());
            });
            return ids;
        },

        initDataTable: function() {
            // Optional: Initialize DataTables for sorting/filtering
            if ($.fn.DataTable) {
                $('#bslm-services-table').DataTable({
                    pageLength: 50,
                    order: [[1, 'asc']], // Sort by service name
                });
            }
        }
    };

    // Initialize on document ready
    $(document).ready(function() {
        BulkPriceEditor.init();
    });

})(jQuery);
```

---

## CSV Import/Export Format

### Export CSV Format

```csv
Service ID,Service Name,Category,Sessions,Regular Price,Member Price,Valid From
123,Facial Treatment,Facial,1,50€,45€,2025-01-01
123,Facial Treatment,Facial,5,225€,200€,2025-01-01
123,Facial Treatment,Facial,10,400€,360€,2025-01-01
124,Laser Hair Removal,Laser,1,80€,72€,2025-01-01
124,Laser Hair Removal,Laser,6,450€,405€,2025-01-01
```

### Import Process

1. User downloads template or exports current prices
2. Edits CSV in Excel/Google Sheets
3. Uploads modified CSV
4. System validates data
5. Shows preview of changes
6. User confirms
7. Prices updated with history logged

---

## PROS - Benefits

### 1. **Time Savings** ⏱️
- Update 100 services in minutes vs. hours
- No need to open individual service edit pages
- Batch operations save clicks

**Example:**
```
Manual editing:    100 services × 2 min each = 200 minutes (3.3 hours)
Bulk editor:       100 services × 5 seconds = 8 minutes
Time saved:        192 minutes = 3.2 hours per update
```

### 2. **Reduced Errors** ✅
- See all prices in one view
- Spot inconsistencies easily
- Percentage calculations automated
- No manual math errors

### 3. **Better Price Management** 💰
- Annual price adjustments simplified
- Apply inflation increases uniformly
- Test different pricing strategies
- A/B pricing experiments

### 4. **Audit Trail** 📋
- Track all price changes
- Know who changed what
- Rollback capability
- Compliance for regulations

### 5. **Data Portability** 📊
- Export for accounting
- Backup pricing data
- Share with managers
- Import from competitors

### 6. **User Experience** 😊
- Intuitive interface
- Familiar spreadsheet-like layout
- Real-time feedback
- Low learning curve

---

## CONS - Challenges

### 1. **Development Complexity** ⚙️

**Medium Complexity** (vs. full price module)

**Estimated Development:**
- Admin page UI: 16 hours
- AJAX handlers: 8 hours
- CSV import/export: 12 hours
- Price history logging: 8 hours
- JavaScript functionality: 12 hours
- Testing: 8 hours
- Documentation: 4 hours
**Total: ~68 hours** ($5,100 @ $75/hr)

### 2. **Performance Concerns** 🐌

**Challenge:** Loading 500+ services at once

**Solution:**
- Pagination (show 50 per page)
- AJAX loading on scroll
- Cache service data
- Database query optimization

```php
// Efficient query with pagination
$args = array(
    'post_type' => 'bslm_service',
    'posts_per_page' => 50,
    'paged' => $paged,
    'fields' => 'ids', // Only load IDs initially
);
```

### 3. **Data Integrity Risks** ⚠️

**Risks:**
- Accidental bulk changes
- CSV import errors
- Concurrent editing conflicts

**Mitigations:**
- Confirmation dialogs for bulk operations
- CSV validation before import
- Show preview of changes
- Undo/rollback capability
- Lock editing during bulk operations

### 4. **UI/UX Complexity** 🎨

**Challenges:**
- Too many services = overwhelming
- Mobile responsiveness difficult
- Inline editing can be cramped

**Solutions:**
- Smart filtering and search
- Collapsible service rows
- Desktop-first approach (mobile view optional)
- Use DataTables for sorting/pagination

### 5. **Database Load** 💾

**Concern:** Price history table grows large

**Management:**
- Auto-cleanup old history (>1 year)
- Pagination on history view
- Archive old records
- Database indexes

```sql
-- Cleanup old history
DELETE FROM wp_bslm_price_changes
WHERE created_at < DATE_SUB(NOW(), INTERVAL 1 YEAR);
```

---

## Implementation Phases

### Phase 1: Basic List View (Week 1-2)
- [x] Admin page with service list
- [x] Display current prices
- [x] Search and filter
- [x] Pagination
**Deliverable:** View-only price list

### Phase 2: Inline Editing (Week 3)
- [x] Edit mode toggle
- [x] AJAX save functionality
- [x] Validation
- [x] Success/error messaging
**Deliverable:** Edit individual services

### Phase 3: Bulk Operations (Week 4)
- [x] Checkbox selection
- [x] Percentage increase/decrease
- [x] Fixed amount adjustment
- [x] Bulk save
**Deliverable:** Update multiple services at once

### Phase 4: Import/Export (Week 5)
- [x] CSV export functionality
- [x] CSV template generation
- [x] CSV import with validation
- [x] Preview before import
**Deliverable:** Spreadsheet integration

### Phase 5: Price History (Week 6)
- [x] Database table for history
- [x] Log all changes
- [x] History view page
- [x] Rollback functionality
**Deliverable:** Full audit trail

### Phase 6: Polish (Week 7)
- [x] Responsive design
- [x] Loading states
- [x] Error handling
- [x] Documentation
- [x] Video tutorial
**Deliverable:** Production-ready feature

---

## Alternative Implementations

### Option A: Simple View-Only List
**Scope:** Just display prices, no editing
**Time:** 8 hours
**Benefit:** Quick to build, still useful for overview

### Option B: Basic Inline Edit (No Bulk)
**Scope:** Edit one service at a time, no bulk operations
**Time:** 24 hours
**Benefit:** Simpler, lower risk

### Option C: CSV-Only Approach
**Scope:** No inline editing, only import/export
**Time:** 16 hours
**Benefit:** Familiar to Excel users, simple to build

### Option D: Full-Featured (RECOMMENDED)
**Scope:** All features listed above
**Time:** 68 hours
**Benefit:** Complete solution, maximum value

---

## User Stories

### Story 1: Annual Price Increase
> "As a salon owner, I want to increase all my service prices by 5% for the new year, so I can adjust for inflation without spending hours updating individual services."

**Solution:** Select all services → Apply 5% increase → Save

### Story 2: New Member Pricing
> "As a salon owner, I want to add member pricing to all my services at 10% off regular price, so I can encourage loyalty program signups."

**Solution:** Bulk edit → Add member_price column → Apply -10% → Save

### Story 3: Price Audit
> "As a manager, I want to see all service prices in one view, so I can spot inconsistencies and ensure competitive pricing."

**Solution:** Open bulk price editor → Sort by price → Review

### Story 4: Competitor Price Match
> "As a salon owner, I want to import prices from a CSV file, so I can match competitor pricing based on my market research."

**Solution:** Export template → Fill with new prices → Import → Preview → Confirm

### Story 5: Price History Review
> "As an accountant, I want to see when prices changed and who changed them, so I can verify revenue projections and ensure compliance."

**Solution:** View price history → Filter by date range → Export report

---

## Mockup Screenshots (Text-based)

### Main View
```
╔══════════════════════════════════════════════════════════════════════╗
║ Bulk Price Editor                                       [? Help]     ║
╠══════════════════════════════════════════════════════════════════════╣
║                                                                      ║
║ 🔍 Search: [_____________]  📁 Category: [All ▼]  🏷️ Tag: [All ▼]   ║
║                                                                      ║
║ ✓ 3 selected   [Apply +10% to selected] [Apply -5€ to selected]    ║
║                                                                      ║
║ ┌──────────────────────────────────────────────────────────────┐   ║
║ │ ☐ │ Service Name       │ Category │ Base Price │ Actions    │   ║
║ ├──────────────────────────────────────────────────────────────┤   ║
║ │ ☑ │ Facial Treatment   │ Facial   │ 50€        │ [Edit]     │   ║
║ │   │  1 seance    50€   │          │            │            │   ║
║ │   │  5 seances   225€  │          │            │            │   ║
║ │   │  10 seances  400€  │          │            │            │   ║
║ ├──────────────────────────────────────────────────────────────┤   ║
║ │ ☑ │ Laser Epilation    │ Laser    │ 80€        │ [Edit]     │   ║
║ │ ☐ │ Massage Therapy    │ Massage  │ 60€        │ [Edit]     │   ║
║ └──────────────────────────────────────────────────────────────┘   ║
║                                                                      ║
║ Showing 1-50 of 127 services                          [1][2][3]>   ║
║                                                                      ║
║ [💾 Export to CSV] [📥 Import from CSV] [📜 View Price History]    ║
║                                                                      ║
╚══════════════════════════════════════════════════════════════════════╝
```

### Inline Edit Mode
```
╔══════════════════════════════════════════════════════════════════════╗
║ Editing: Facial Treatment                                           ║
╠══════════════════════════════════════════════════════════════════════╣
║ ┌──────────────────────────────────────────────────────────────┐   ║
║ │ Sessions │ Regular Price │ Member Price │ Valid From         │   ║
║ ├──────────────────────────────────────────────────────────────┤   ║
║ │ [_1_]    │ [__50€___]   │ [__45€___]  │ [2025-01-01]       │   ║
║ │ [_5_]    │ [__225€__]   │ [__200€__]  │ [2025-01-01]       │   ║
║ │ [_10_]   │ [__400€__]   │ [__360€__]  │ [2025-01-01]       │   ║
║ │          │ [+ Add Tier]  │             │                    │   ║
║ └──────────────────────────────────────────────────────────────┘   ║
║                                                                      ║
║ [💾 Save Changes] [❌ Cancel]                                       ║
╚══════════════════════════════════════════════════════════════════════╝
```

---

## Cost-Benefit Analysis

### Development Investment
- **68 hours @ $75/hr = $5,100**

### Time Savings (Annual)
Assuming salon updates prices 4 times per year:
```
Manual: 4 updates × 3 hours = 12 hours
Bulk:   4 updates × 15 min = 1 hour
Saved:  11 hours per year per salon

Value: 11 hours × $50/hr (salon owner rate) = $550/year saved per salon
```

### Break-Even
- Need ~10 salons using this feature to justify cost
- Given 100+ potential users, ROI is strong

---

## Recommendation

### ✅ **HIGHLY RECOMMENDED**

**Reasons:**
1. ✅ High user value (saves significant time)
2. ✅ Moderate complexity (manageable development)
3. ✅ Standalone feature (doesn't require full price module)
4. ✅ Clear use cases (annual increases, member pricing)
5. ✅ Competitive advantage (most plugins lack this)

**Suggested Approach:**
- **Start with Phase 1-3** (Basic list + inline edit + bulk operations)
- **Time: ~4 weeks**
- **Cost: ~$4,000**
- **Add CSV and history later if requested**

---

## Next Steps

### If Approved

**Week 1:**
- [ ] Create admin page skeleton
- [ ] Design database schema for history
- [ ] Create UI mockups in Figma/Sketch

**Week 2:**
- [ ] Build service list view
- [ ] Implement search and filters
- [ ] Add pagination

**Week 3:**
- [ ] Build inline editing interface
- [ ] Implement AJAX save handlers
- [ ] Add validation

**Week 4:**
- [ ] Add bulk operations (percentage, fixed)
- [ ] Add selection checkboxes
- [ ] Implement confirmation dialogs

**Week 5:**
- [ ] Build CSV export
- [ ] Build CSV import with preview
- [ ] Add validation

**Week 6:**
- [ ] Add price history logging
- [ ] Create history view page
- [ ] Add rollback functionality

**Week 7:**
- [ ] Polish UI/UX
- [ ] Write documentation
- [ ] Create video tutorial
- [ ] Testing
- [ ] Launch as v1.5.0

---

**Document Version:** 1.0
**Last Updated:** 2025-12-16
**Status:** Proposal - Awaiting Approval
**Estimated Release:** v1.5.0 (7 weeks from approval)
