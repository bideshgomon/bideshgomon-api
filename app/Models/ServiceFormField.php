<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceFormField extends Model
{
    protected $fillable = [
        'service_module_id',
        'field_name',
        'field_label',
        'field_type',
        'placeholder',
        'help_text',
        'default_value',
        'is_required',
        'validation_rules',
        'min_length',
        'max_length',
        'profile_map_key',
        'profile_map_table',
        'profile_map_column',
        'options',
        'allow_multiple',
        'accepted_file_types',
        'max_file_size',
        'conditional_rules',
        'sort_order',
        'group_name',
        'section_name',
        'column_width',
        'css_class',
        'is_active',
        'is_readonly',
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'is_active' => 'boolean',
        'is_readonly' => 'boolean',
        'allow_multiple' => 'boolean',
        'options' => 'array',
        'conditional_rules' => 'array',
        'sort_order' => 'integer',
        'column_width' => 'integer',
        'min_length' => 'integer',
        'max_length' => 'integer',
        'max_file_size' => 'integer',
    ];

    /**
     * Field types enum
     */
    const FIELD_TYPES = [
        'text' => 'Text Input',
        'textarea' => 'Text Area',
        'email' => 'Email',
        'tel' => 'Phone Number',
        'number' => 'Number',
        'date' => 'Date',
        'datetime' => 'Date & Time',
        'time' => 'Time',
        'file' => 'File Upload',
        'select' => 'Dropdown Select',
        'radio' => 'Radio Buttons',
        'checkbox' => 'Checkboxes',
        'url' => 'URL',
        'password' => 'Password',
    ];

    /**
     * Get the service module this field belongs to
     */
    public function serviceModule(): BelongsTo
    {
        return $this->belongsTo(ServiceModule::class);
    }

    /**
     * Scope: Active fields only
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Ordered by sort_order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }

    /**
     * Scope: Grouped fields
     */
    public function scopeGrouped($query)
    {
        return $query->orderBy('group_name')->orderBy('sort_order');
    }

    /**
     * Scope: Fields by section (for multi-step forms)
     */
    public function scopeInSection($query, $sectionName)
    {
        return $query->where('section_name', $sectionName);
    }

    /**
     * Check if field has profile mapping
     */
    public function hasProfileMapping(): bool
    {
        return !empty($this->profile_map_key) || 
               (!empty($this->profile_map_table) && !empty($this->profile_map_column));
    }

    /**
     * Get full profile map path (for dot notation access)
     */
    public function getProfileMapPath(): ?string
    {
        if ($this->profile_map_key) {
            return $this->profile_map_key;
        }
        
        if ($this->profile_map_table && $this->profile_map_column) {
            return "{$this->profile_map_table}.{$this->profile_map_column}";
        }
        
        return null;
    }

    /**
     * Get Laravel validation rules string
     */
    public function getValidationRulesArray(): array
    {
        $rules = [];
        
        if ($this->is_required) {
            $rules[] = 'required';
        } else {
            $rules[] = 'nullable';
        }
        
        // Add custom validation rules
        if ($this->validation_rules) {
            $customRules = explode('|', $this->validation_rules);
            $rules = array_merge($rules, $customRules);
        }
        
        // Add length constraints
        if ($this->min_length && $this->max_length) {
            $rules[] = "between:{$this->min_length},{$this->max_length}";
        } elseif ($this->min_length) {
            $rules[] = "min:{$this->min_length}";
        } elseif ($this->max_length) {
            $rules[] = "max:{$this->max_length}";
        }
        
        // Field type specific rules
        switch ($this->field_type) {
            case 'email':
                $rules[] = 'email';
                break;
            case 'url':
                $rules[] = 'url';
                break;
            case 'number':
                $rules[] = 'numeric';
                break;
            case 'date':
                $rules[] = 'date';
                break;
            case 'datetime':
                $rules[] = 'date';
                break;
            case 'file':
                if ($this->accepted_file_types) {
                    $extensions = str_replace('.', '', $this->accepted_file_types);
                    $rules[] = "mimes:{$extensions}";
                }
                if ($this->max_file_size) {
                    $rules[] = "max:{$this->max_file_size}";
                }
                break;
        }
        
        return $rules;
    }

    /**
     * Check if field should be shown based on conditional rules
     * 
     * @param array $formData Current form data
     * @return bool
     */
    public function shouldShow(array $formData): bool
    {
        if (!$this->conditional_rules) {
            return true;
        }
        
        $rules = $this->conditional_rules;
        
        // Simple show_if rule
        if (isset($rules['show_if'])) {
            $condition = $rules['show_if'];
            $fieldName = $condition['field'] ?? null;
            $expectedValue = $condition['value'] ?? null;
            
            if ($fieldName && isset($formData[$fieldName])) {
                return $formData[$fieldName] == $expectedValue;
            }
            
            return false;
        }
        
        // Hide_if rule
        if (isset($rules['hide_if'])) {
            $condition = $rules['hide_if'];
            $fieldName = $condition['field'] ?? null;
            $expectedValue = $condition['value'] ?? null;
            
            if ($fieldName && isset($formData[$fieldName])) {
                return $formData[$fieldName] != $expectedValue;
            }
            
            return true;
        }
        
        return true;
    }

    /**
     * Get field options as array
     */
    public function getOptionsArray(): array
    {
        if (!$this->options) {
            return [];
        }
        
        // If options are already in format [{"value": "m", "label": "Male"}]
        if (is_array($this->options) && isset($this->options[0]) && is_array($this->options[0])) {
            return $this->options;
        }
        
        // Convert simple array ["Male", "Female"] to [{"value": "male", "label": "Male"}]
        return collect($this->options)->map(function ($label) {
            return [
                'value' => strtolower(str_replace(' ', '_', $label)),
                'label' => $label,
            ];
        })->toArray();
    }
}
