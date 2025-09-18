# Dynamic Content Plugin - Testing Guide

## What This Plugin Does

The **Dynamic Content Plugin** creates a custom WordPress post type (`dynamic_content`) for managing dynamic content programmatically. Content is created and accessed via PHP code, not the WordPress admin UI, and rendered using PHP templates.

**Key Features:**
- Custom post type for dynamic content storage
- Template-based rendering (`trigger.tpl.php`, `trigger-{slug}.tpl.php`)
- Programmatic content access via slug
- Built-in caching system
- No admin UI (intentional design)

## Quick Testing Checklist

### 1. Verify Plugin is Working
```bash
# connect to docker container if started with wp-env
wp-env run cli bash
```
```bash
# Check if post type is registered
wp post-type list
```
**Expected:** Should show `dynamic_content` with "Dynamic Content" label.

### 2. Test Core Functionality
```bash
# Create test content
wp post create --post_type=dynamic_content --post_title="Test Content" --post_name="test-slug" --post_status=publish

# List dynamic content
wp post list --post_type=dynamic_content

# Test plugin methods
wp eval '
global $dynamic_content_plugin;
if ($dynamic_content_plugin) {
    echo "✅ Plugin loaded\n";
    $contents = $dynamic_content_plugin->get_contents();
    echo "Found " . count($contents) . " items\n";
    $test = $dynamic_content_plugin->get_content_by_slug("test-slug");
    echo ($test ? "✅ Found by slug: " . $test->post_title : "❌ Not found") . "\n";
} else {
    echo "❌ Plugin not loaded\n";
}'
```

## Expected Results

**Successful Test Output:**
```
✅ Plugin loaded
Found 1 items
✅ Found by slug: Test Content
```