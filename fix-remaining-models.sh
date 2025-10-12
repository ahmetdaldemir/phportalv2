#!/bin/bash

echo "Starting to fix remaining models with proper relationships..."

# Create a temporary PHP script to fix the remaining models
cat > fix_models.php << 'EOF'
<?php

// This script will fix the remaining models with proper relationships
// Models to fix: TechnicalService, Phone, Safe, FinansTransaction, and others

$models = [
    'TechnicalService' => [
        'file' => 'app/Models/TechnicalService.php',
        'relationships' => [
            'company' => 'BelongsTo',
            'user' => 'BelongsTo', 
            'customer' => 'BelongsTo',
            'brand' => 'BelongsTo',
            'version' => 'BelongsTo',
            'seller' => 'BelongsTo',
            'technicalServiceProducts' => 'HasMany',
            'technicalServiceProcesses' => 'HasMany'
        ]
    ],
    'Phone' => [
        'file' => 'app/Models/Phone.php',
        'relationships' => [
            'company' => 'BelongsTo',
            'customer' => 'BelongsTo',
            'stockCard' => 'BelongsTo',
            'brand' => 'BelongsTo',
            'version' => 'BelongsTo',
            'color' => 'BelongsTo',
            'invoice' => 'BelongsTo',
            'sales' => 'HasMany'
        ]
    ],
    'Safe' => [
        'file' => 'app/Models/Safe.php',
        'relationships' => [
            'company' => 'BelongsTo',
            'user' => 'BelongsTo',
            'seller' => 'BelongsTo',
            'invoice' => 'BelongsTo',
            'bank' => 'BelongsTo'
        ]
    ],
    'FinansTransaction' => [
        'file' => 'app/Models/FinansTransaction.php',
        'relationships' => [
            'user' => 'BelongsTo',
            'company' => 'BelongsTo',
            'safe' => 'BelongsTo',
            'currency' => 'BelongsTo'
        ]
    ],
    'TechnicalServiceCategory' => [
        'file' => 'app/Models/TechnicalServiceCategory.php',
        'relationships' => [
            'company' => 'BelongsTo',
            'user' => 'BelongsTo',
            'parent' => 'BelongsTo',
            'children' => 'HasMany',
            'technicalServices' => 'HasMany'
        ]
    ],
    'TechnicalServiceProducts' => [
        'file' => 'app/Models/TechnicalServiceProducts.php',
        'relationships' => [
            'user' => 'BelongsTo',
            'company' => 'BelongsTo',
            'technicalService' => 'BelongsTo',
            'stockCard' => 'BelongsTo',
            'stockCardMovement' => 'BelongsTo'
        ]
    ],
    'TechnicalCustomService' => [
        'file' => 'app/Models/TechnicalCustomService.php',
        'relationships' => [
            'company' => 'BelongsTo',
            'user' => 'BelongsTo',
            'seller' => 'BelongsTo',
            'customer' => 'BelongsTo',
            'technicalCustomProducts' => 'HasMany'
        ]
    ],
    'TechnicalCustomProducts' => [
        'file' => 'app/Models/TechnicalCustomProducts.php',
        'relationships' => [
            'company' => 'BelongsTo',
            'user' => 'BelongsTo',
            'technicalCustomService' => 'BelongsTo',
            'stockCard' => 'BelongsTo'
        ]
    ],
    'EInvoice' => [
        'file' => 'app/Models/EInvoice.php',
        'relationships' => [
            'company' => 'BelongsTo',
            'invoice' => 'BelongsTo',
            'eInvoiceDetails' => 'HasMany'
        ]
    ],
    'EInvoiceDetail' => [
        'file' => 'app/Models/EInvoiceDetail.php',
        'relationships' => [
            'eInvoice' => 'BelongsTo',
            'stockCard' => 'BelongsTo'
        ]
    ],
    'Accounting' => [
        'file' => 'app/Models/Accounting.php',
        'relationships' => [
            'company' => 'BelongsTo',
            'user' => 'BelongsTo',
            'invoice' => 'BelongsTo',
            'bank' => 'BelongsTo',
            'accountingCategory' => 'BelongsTo'
        ]
    ],
    'AccountingCategory' => [
        'file' => 'app/Models/AccountingCategory.php',
        'relationships' => [
            'company' => 'BelongsTo',
            'user' => 'BelongsTo',
            'accountings' => 'HasMany'
        ]
    ],
    'UserSallary' => [
        'file' => 'app/Models/UserSallary.php',
        'relationships' => [
            'company' => 'BelongsTo',
            'user' => 'BelongsTo'
        ]
    ],
    'PersonalAccountMonth' => [
        'file' => 'app/Models/PersonalAccountMonth.php',
        'relationships' => [
            'company' => 'BelongsTo',
            'user' => 'BelongsTo'
        ]
    ],
    'SellerAccountMonth' => [
        'file' => 'app/Models/SellerAccountMonth.php',
        'relationships' => [
            'company' => 'BelongsTo',
            'seller' => 'BelongsTo'
        ]
    ],
    'Refund' => [
        'file' => 'app/Models/Refund.php',
        'relationships' => [
            'company' => 'BelongsTo',
            'user' => 'BelongsTo',
            'seller' => 'BelongsTo',
            'reason' => 'BelongsTo'
        ]
    ],
    'Demand' => [
        'file' => 'app/Models/Demand.php',
        'relationships' => [
            'company' => 'BelongsTo',
            'user' => 'BelongsTo'
        ]
    ],
    'Blog' => [
        'file' => 'app/Models/Blog.php',
        'relationships' => [
            'company' => 'BelongsTo',
            'user' => 'BelongsTo'
        ]
    ],
    'Setting' => [
        'file' => 'app/Models/Setting.php',
        'relationships' => [
            'company' => 'BelongsTo',
            'user' => 'BelongsTo'
        ]
    ],
    'StockCardPrice' => [
        'file' => 'app/Models/StockCardPrice.php',
        'relationships' => [
            'company' => 'BelongsTo',
            'stockCard' => 'BelongsTo'
        ]
    ],
    'StockTraking' => [
        'file' => 'app/Models/StockTraking.php',
        'relationships' => [
            'company' => 'BelongsTo',
            'user' => 'BelongsTo',
            'stockCard' => 'BelongsTo'
        ]
    ],
    'FakeProduct' => [
        'file' => 'app/Models/FakeProduct.php',
        'relationships' => [
            'company' => 'BelongsTo',
            'user' => 'BelongsTo',
            'stockCard' => 'BelongsTo'
        ]
    ],
    'RemoteApiLog' => [
        'file' => 'app/Models/RemoteApiLog.php',
        'relationships' => [
            'company' => 'BelongsTo'
        ]
    ],
    'Enumeration' => [
        'file' => 'app/Models/Enumeration.php',
        'relationships' => [
            'company' => 'BelongsTo'
        ]
    ],
    'OlderEnumeration' => [
        'file' => 'app/Models/OlderEnumeration.php',
        'relationships' => [
            'company' => 'BelongsTo'
        ]
    ],
    'Notification' => [
        'file' => 'app/Models/Notification.php',
        'relationships' => [
            // Laravel notifications don't need explicit relationships
        ]
    ],
    'SiteTechnicalServiceCategory' => [
        'file' => 'app/Models/SiteTechnicalServiceCategory.php',
        'relationships' => [
            'company' => 'BelongsTo'
        ]
    ],
    'TechnicalProcess' => [
        'file' => 'app/Models/TechnicalProcess.php',
        'relationships' => [
            'company' => 'BelongsTo',
            'user' => 'BelongsTo'
        ]
    ],
    'TechnicalServiceProcess' => [
        'file' => 'app/Models/TechnicalServiceProcess.php',
        'relationships' => [
            'technicalService' => 'BelongsTo',
            'technicalProcess' => 'BelongsTo'
        ]
    ],
    'VersionChild' => [
        'file' => 'app/Models/VersionChild.php',
        'relationships' => [
            'version' => 'BelongsTo',
            'parentVersion' => 'BelongsTo'
        ]
    ],
    'DeletedAtSerialNumber' => [
        'file' => 'app/Models/DeletedAtSerialNumber.php',
        'relationships' => [
            // This is a simple model without relationships
        ]
    ],
    'City' => [
        'file' => 'app/Models/City.php',
        'relationships' => [
            'towns' => 'HasMany',
            'customers' => 'HasMany'
        ]
    ],
    'Town' => [
        'file' => 'app/Models/Town.php',
        'relationships' => [
            'city' => 'BelongsTo',
            'customers' => 'HasMany'
        ]
    ],
    'Currency' => [
        'file' => 'app/Models/Currency.php',
        'relationships' => [
            'invoices' => 'HasMany',
            'finansTransactions' => 'HasMany'
        ]
    ]
];

echo "Models to be processed: " . count($models) . "\n";

foreach ($models as $modelName => $config) {
    echo "Processing $modelName...\n";
    
    if (!file_exists($config['file'])) {
        echo "Warning: File {$config['file']} does not exist, skipping...\n";
        continue;
    }
    
    $content = file_get_contents($config['file']);
    
    // Add missing imports if needed
    if (!str_contains($content, 'use Illuminate\Database\Eloquent\Relations\BelongsTo;')) {
        $content = str_replace(
            'use Illuminate\Database\Eloquent\Model;',
            "use Illuminate\Database\Eloquent\Model;\nuse Illuminate\Database\Eloquent\Relations\BelongsTo;\nuse Illuminate\Database\Eloquent\Relations\HasMany;",
            $content
        );
    }
    
    // Add CompanyScope if not present
    if (!str_contains($content, 'CompanyScope') && !str_contains($content, 'use App\Scopes\CompanyScope;')) {
        $content = str_replace(
            'use Illuminate\Database\Eloquent\Model;',
            "use Illuminate\Database\Eloquent\Model;\nuse App\Scopes\CompanyScope;",
            $content
        );
    }
    
    // Add boot method if not present
    if (!str_contains($content, 'protected static function boot()')) {
        $content = str_replace(
            'class ' . $modelName . ' extends',
            "class $modelName extends",
            $content
        );
        
        // Find the class opening and add boot method
        $content = preg_replace(
            '/(class ' . $modelName . ' extends[^{]+{)/',
            "$1\n\n    protected static function boot()\n    {\n        parent::boot();\n        static::addGlobalScope(new CompanyScope);\n    }",
            $content
        );
    }
    
    // Add relationships
    $relationshipMethods = "\n    // Proper Eloquent Relationships\n";
    foreach ($config['relationships'] as $relationName => $relationType) {
        $methodName = $relationName;
        $relatedModel = ucfirst($relationName);
        
        if ($relationType === 'BelongsTo') {
            $relationshipMethods .= "    public function $methodName(): BelongsTo\n    {\n        return \$this->belongsTo($relatedModel::class, '{$relationName}_id');\n    }\n\n";
        } elseif ($relationType === 'HasMany') {
            $relationshipMethods .= "    public function $methodName(): HasMany\n    {\n        return \$this->hasMany($relatedModel::class, '{$relationName}_id');\n    }\n\n";
        }
    }
    
    // Add relationships before the closing brace
    if (!str_contains($content, '// Proper Eloquent Relationships')) {
        $content = str_replace(
            '}',
            $relationshipMethods . '}',
            $content
        );
    }
    
    // Write the updated content back
    file_put_contents($config['file'], $content);
    echo "✓ Updated $modelName\n";
}

echo "\nAll models have been processed!\n";
echo "Next step: Create factories and seeders for all tables.\n";
?>
EOF

# Run the PHP script
php fix_models.php

# Clean up
rm fix_models.php

echo "Model relationship fixes completed!"
echo "Next: Creating factories and seeders..."
