<?php
if (!defined('ABSPATH')) {
    exit;
}

class SOP_JSON_Validator {

    private $errors = array();
    private $warnings = array();

    public function validate_json($json_string) {
        $this->errors = array();
        $this->warnings = array();

        // Basic JSON syntax validation
        $data = json_decode($json_string);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->errors[] = 'JSON Syntax Error: ' . json_last_error_msg();
            return false;
        }

        // Structure validation
        return $this->validate_structure($data);
    }

    private function validate_structure($data) {
        // Must be an object
        if (!is_object($data)) {
            $this->errors[] = 'Root element must be an object';
            return false;
        }

        // Validate required fields
        if (!isset($data->title) || empty($data->title)) {
            $this->warnings[] = 'Title field is missing or empty';
        }

        if (!isset($data->sections) || !is_array($data->sections)) {
            $this->errors[] = 'Sections array is required';
            return false;
        }

        // Validate sections
        foreach ($data->sections as $index => $section) {
            if (!$this->validate_section($section, $index)) {
                return false;
            }
        }

        return true;
    }

    private function validate_section($section, $index) {
        if (!is_object($section)) {
            $this->errors[] = "Section {$index} must be an object";
            return false;
        }

        if (!isset($section->title) || empty($section->title)) {
            $this->errors[] = "Section {$index} missing or empty title";
            return false;
        }

        if (!isset($section->content) || empty($section->content)) {
            $this->warnings[] = "Section '{$section->title}' has no content";
        } else {
            // Validate content type (string or array)
            if (is_string($section->content)) {
                // Regular HTML content - no additional validation needed
            } else if (is_array($section->content)) {
                // Array of content objects - validate each item
                foreach ($section->content as $content_index => $content_item) {
                    if (!$this->validate_content_item($content_item, $index, $content_index)) {
                        return false;
                    }
                }
            } else {
                $this->errors[] = "Section '{$section->title}' content must be a string or array";
                return false;
            }
        }

        // Validate subsections jika ada
        if (isset($section->subsections) && is_array($section->subsections)) {
            foreach ($section->subsections as $sub_index => $subsection) {
                if (!$this->validate_section($subsection, "{$index}.{$sub_index}")) {
                    return false;
                }
            }
        }

        return true;
    }

    private function validate_content_item($content_item, $section_index, $content_index) {
        if (!is_object($content_item)) {
            $this->errors[] = "Content item {$section_index}.{$content_index} must be an object";
            return false;
        }

        // Validate required fields
        if (!isset($content_item->type) || empty($content_item->type)) {
            $this->errors[] = "Content item {$section_index}.{$content_index} missing type field";
            return false;
        }

        // Validate based on type
        switch ($content_item->type) {
            case 'link':
                return $this->validate_link_content($content_item, $section_index, $content_index);
            
            default:
                $this->warnings[] = "Content item {$section_index}.{$content_index} has unknown type '{$content_item->type}'";
                return true;
        }
    }

    private function validate_link_content($content_item, $section_index, $content_index) {
        // Validate required fields for link type
        if (!isset($content_item->title) || empty($content_item->title)) {
            $this->errors[] = "Link content item {$section_index}.{$content_index} missing title field";
            return false;
        }

        if (!isset($content_item->url) || empty($content_item->url)) {
            $this->errors[] = "Link content item {$section_index}.{$content_index} missing url field";
            return false;
        }

        // Validate URL format
        if (!filter_var($content_item->url, FILTER_VALIDATE_URL) && !str_starts_with($content_item->url, '/')) {
            $this->warnings[] = "Link content item {$section_index}.{$content_index} has potentially invalid URL: {$content_item->url}";
        }

        // Validate optional target field
        if (isset($content_item->target) && !empty($content_item->target)) {
            $valid_targets = array('_blank', '_self', '_parent', '_top');
            if (!in_array($content_item->target, $valid_targets)) {
                $this->warnings[] = "Link content item {$section_index}.{$content_index} has invalid target '{$content_item->target}'. Should be one of: " . implode(', ', $valid_targets);
            }
        }

        return true;
    }

    public function get_errors() {
        return $this->errors;
    }

    public function get_warnings() {
        return $this->warnings;
    }

    public function has_errors() {
        return !empty($this->errors);
    }

    public function has_warnings() {
        return !empty($this->warnings);
    }
}