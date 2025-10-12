<?php

/**
 * SQL Schema'dan Otomatik Migration Oluşturucu
 * 
 * Bu script SqlShemaDump.sql dosyasını okuyup her tablo için migration oluşturur
 */

class MigrationGenerator
{
    private $sqlFile = 'SqlShemaDump.sql';
    private $migrationsPath = 'database/migrations/';
    private $timestamp = '2024_01_01_';
    private $counter = 1000;
    
    // Tablo dependency sıralaması
    private $tableOrder = [
        // Level 1: No dependencies
        'cities',
        'currencies',
        'companies',
        
        // Level 2: Company dependent
        'sellers',
        'users',
        
        // Level 3: Spatie Permission
        'permissions',
        'roles',
        'model_has_permissions',
        'model_has_roles',
        'role_has_permissions',
        'personal_access_tokens',
        
        // Level 4: Master data
        'brands',
        'categories',
        'colors',
        'warehouses',
        'reasons',
        'versions',
        'version_children',
        
        // Level 5: Customers & Finance
        'customers',
        'banks',
        'accounting_categories',
        'accountings',
        'safes',
        
        // Level 6: Stock management
        'stock_cards',
        'stock_card_prices',
        
        // Level 7: Invoices
        'invoices',
        'stock_card_movements',
        
        // Level 8: Sales & Phones
        'sales',
        'phones',
        
        // Level 9: Transfers & Demands
        'transfers',
        'demands',
        'refunds',
        
        // Level 10: Technical Services
        'technical_service_categories',
        'technical_services',
        'technical_service_processes',
        'technical_service_products',
        'technical_custom_services',
        'technical_custom_products',
        'site_technical_service_categories',
        
        // Level 11: E-Invoice
        'e_invoices',
        'e_invoice_details',
        
        // Level 12: Finance transactions
        'finans_transactions',
        'transactions',
        'personal_account_months',
        'seller_account_months',
        'user_sallaries',
        
        // Level 13: Others
        'notifications',
        'enumerations',
        'older_enumerations',
        'stock_trakings',
        'blogs',
        'settings',
        'fake_products',
        'remote_api_logs',
        'deleted_at_serial_numbers',
        'activity_log',
        'laravel_logger_activity',
        
        // Level 14: Laravel system
        'failed_jobs',
        'jobs',
        'password_resets',
    ];
    
    public function run()
    {
        echo "🚀 Migration oluşturma başlıyor...\n";
        echo "===================================\n\n";
        
        if (!file_exists($this->sqlFile)) {
            die("❌ Hata: {$this->sqlFile} dosyası bulunamadı!\n");
        }
        
        $sqlContent = file_get_contents($this->sqlFile);
        $tables = $this->parseSqlTables($sqlContent);
        
        echo "📊 Toplam " . count($tables) . " tablo bulundu\n\n";
        
        $createdCount = 0;
        $skippedCount = 0;
        
        foreach ($this->tableOrder as $tableName) {
            if (isset($tables[$tableName])) {
                echo "📝 Oluşturuluyor: {$tableName}\n";
                $this->createMigration($tableName, $tables[$tableName]);
                $createdCount++;
            } else {
                echo "⚠️  Atlanıyor: {$tableName} (SQL'de bulunamadı)\n";
                $skippedCount++;
            }
        }
        
        // tableOrder'da olmayan tabloları da kontrol et
        foreach ($tables as $tableName => $tableInfo) {
            if (!in_array($tableName, $this->tableOrder)) {
                echo "⚠️  Sıralamada yok: {$tableName}\n";
                $this->createMigration($tableName, $tableInfo);
                $createdCount++;
            }
        }
        
        echo "\n===================================\n";
        echo "✅ Oluşturulan: {$createdCount}\n";
        echo "⚠️  Atlanan: {$skippedCount}\n";
        echo "✅ Tamamlandı!\n";
    }
    
    private function parseSqlTables($sqlContent)
    {
        $tables = [];
        
        // Her CREATE TABLE statement'ı bul
        preg_match_all('/CREATE TABLE `(\w+)`\s*\((.*?)\) ENGINE/s', $sqlContent, $matches, PREG_SET_ORDER);
        
        foreach ($matches as $match) {
            $tableName = $match[1];
            $tableContent = $match[2];
            
            if ($tableName === 'testmarkamodel' || $tableName === 'migrations') {
                continue; // Test ve migrations tablosunu atla
            }
            
            $tables[$tableName] = [
                'name' => $tableName,
                'content' => $tableContent,
                'columns' => $this->parseColumns($tableContent),
                'indexes' => $this->parseIndexes($tableContent),
                'foreignKeys' => $this->parseForeignKeys($tableContent)
            ];
        }
        
        return $tables;
    }
    
    private function parseColumns($tableContent)
    {
        $columns = [];
        $lines = explode("\n", $tableContent);
        
        foreach ($lines as $line) {
            $line = trim($line);
            
            // Kolon tanımını bul (backtick ile başlayan)
            if (preg_match('/^`(\w+)`\s+(.+?)(,|\s*$)/', $line, $match)) {
                $columnName = $match[1];
                $columnDef = trim($match[2]);
                
                // PRIMARY KEY, INDEX, CONSTRAINT satırlarını atla
                if (in_array($columnName, ['PRIMARY', 'INDEX', 'UNIQUE', 'KEY', 'CONSTRAINT'])) {
                    continue;
                }
                
                $columns[$columnName] = [
                    'name' => $columnName,
                    'definition' => $columnDef,
                    'type' => $this->parseColumnType($columnDef),
                    'nullable' => stripos($columnDef, 'NULL') !== false && stripos($columnDef, 'NOT NULL') === false,
                    'default' => $this->parseDefault($columnDef),
                    'comment' => $this->parseComment($columnDef)
                ];
            }
        }
        
        return $columns;
    }
    
    private function parseColumnType($definition)
    {
        // Tip parse et
        if (preg_match('/^(\w+)(\([^)]+\))?/', $definition, $match)) {
            $baseType = strtolower($match[1]);
            $params = isset($match[2]) ? $match[2] : '';
            
            return [
                'base' => $baseType,
                'params' => $params
            ];
        }
        
        return ['base' => 'string', 'params' => ''];
    }
    
    private function parseDefault($definition)
    {
        if (preg_match('/DEFAULT\s+([^\s,]+)/i', $definition, $match)) {
            $default = trim($match[1], "'\"");
            if ($default === 'NULL') {
                return null;
            }
            if ($default === 'CURRENT_TIMESTAMP') {
                return 'CURRENT_TIMESTAMP';
            }
            return $default;
        }
        return null;
    }
    
    private function parseComment($definition)
    {
        if (preg_match('/COMMENT\s+[\'"](.+?)[\'"]/i', $definition, $match)) {
            return $match[1];
        }
        return null;
    }
    
    private function parseIndexes($tableContent)
    {
        $indexes = [];
        
        if (preg_match_all('/(?:UNIQUE\s+)?INDEX\s+`?(\w+)`?\s*\(`?([^`\)]+)`?\s*(?:\([^\)]+\))?\s*(?:ASC|DESC)?/i', $tableContent, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $indexes[] = [
                    'name' => $match[1],
                    'columns' => array_map('trim', explode(',', str_replace(['`', '(768)', '(', ')'], '', $match[2]))),
                    'unique' => stripos($match[0], 'UNIQUE') !== false
                ];
            }
        }
        
        return $indexes;
    }
    
    private function parseForeignKeys($tableContent)
    {
        $foreignKeys = [];
        
        if (preg_match_all('/CONSTRAINT\s+`(\w+)`\s+FOREIGN KEY\s+\(`(\w+)`\)\s+REFERENCES\s+`(\w+)`\s+\(`(\w+)`\)(?:\s+ON DELETE\s+(\w+))?(?:\s+ON UPDATE\s+(\w+))?/i', $tableContent, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $foreignKeys[] = [
                    'name' => $match[1],
                    'column' => $match[2],
                    'references_table' => $match[3],
                    'references_column' => $match[4],
                    'on_delete' => isset($match[5]) ? strtolower($match[5]) : 'restrict',
                    'on_update' => isset($match[6]) ? strtolower($match[6]) : 'restrict'
                ];
            }
        }
        
        return $foreignKeys;
    }
    
    private function createMigration($tableName, $tableInfo)
    {
        $className = $this->getClassName($tableName);
        $fileName = $this->getFileName($tableName);
        $filePath = $this->migrationsPath . $fileName;
        
        $content = $this->generateMigrationContent($className, $tableName, $tableInfo);
        
        file_put_contents($filePath, $content);
        $this->counter++;
    }
    
    private function getClassName($tableName)
    {
        return 'Create' . str_replace('_', '', ucwords($tableName, '_')) . 'Table';
    }
    
    private function getFileName($tableName)
    {
        $timestamp = $this->timestamp . str_pad($this->counter, 6, '0', STR_PAD_LEFT);
        return $timestamp . '_create_' . $tableName . '_table.php';
    }
    
    private function generateMigrationContent($className, $tableName, $tableInfo)
    {
        $content = "<?php\n\n";
        $content .= "use Illuminate\\Database\\Migrations\\Migration;\n";
        $content .= "use Illuminate\\Database\\Schema\\Blueprint;\n";
        $content .= "use Illuminate\\Support\\Facades\\Schema;\n\n";
        $content .= "return new class extends Migration\n";
        $content .= "{\n";
        $content .= "    /**\n";
        $content .= "     * Run the migrations.\n";
        $content .= "     */\n";
        $content .= "    public function up(): void\n";
        $content .= "    {\n";
        $content .= "        Schema::create('{$tableName}', function (Blueprint \$table) {\n";
        
        // Kolonları ekle
        foreach ($tableInfo['columns'] as $column) {
            $content .= $this->generateColumnDefinition($column);
        }
        
        // Timestamps ekle (eğer created_at veya updated_at varsa)
        $hasCreatedAt = isset($tableInfo['columns']['created_at']);
        $hasUpdatedAt = isset($tableInfo['columns']['updated_at']);
        $hasDeletedAt = isset($tableInfo['columns']['deleted_at']);
        
        if ($hasCreatedAt || $hasUpdatedAt) {
            $content .= "            \$table->timestamps();\n";
        }
        
        if ($hasDeletedAt) {
            $content .= "            \$table->softDeletes();\n";
        }
        
        // Indexleri ekle (foreign key olmayanları)
        foreach ($tableInfo['indexes'] as $index) {
            if (!$this->isForeignKeyIndex($index['name'])) {
                $content .= $this->generateIndexDefinition($index);
            }
        }
        
        // Foreign key'leri ekle (duplicate'leri filtrele)
        $addedForeignKeys = [];
        foreach ($tableInfo['foreignKeys'] as $fk) {
            $fkKey = $fk['column'] . '_' . $fk['references_table'];
            if (!in_array($fkKey, $addedForeignKeys)) {
                $content .= $this->generateForeignKeyDefinition($fk);
                $addedForeignKeys[] = $fkKey;
            }
        }
        
        $content .= "        });\n";
        $content .= "    }\n\n";
        $content .= "    /**\n";
        $content .= "     * Reverse the migrations.\n";
        $content .= "     */\n";
        $content .= "    public function down(): void\n";
        $content .= "    {\n";
        $content .= "        Schema::dropIfExists('{$tableName}');\n";
        $content .= "    }\n";
        $content .= "};\n";
        
        return $content;
    }
    
    private function generateColumnDefinition($column)
    {
        $name = $column['name'];
        $type = $column['type'];
        $nullable = $column['nullable'];
        $default = $column['default'];
        $comment = $column['comment'];
        
        // Skip id if it's auto increment bigint or int
        if ($name === 'id' && (in_array($type['base'], ['bigint', 'int']))) {
            if ($type['base'] === 'bigint') {
                return "            \$table->id();\n";
            } else {
                return "            \$table->increments('id');\n";
            }
        }
        
        // Timestamps - don't skip, will be added at end
        if ($name === 'created_at' || $name === 'updated_at') {
            return ''; // Will be added by timestamps()
        }
        
        if ($name === 'deleted_at') {
            return ''; // Will be added by softDeletes()
        }
        
        // Map SQL types to Laravel types
        $line = "            \$table->" . $this->mapTypeToLaravel($name, $type, $default);
        
        if ($nullable) {
            $line .= "->nullable()";
        }
        
        if ($default !== null && $default !== 'CURRENT_TIMESTAMP' && $name !== 'id') {
            $defaultValue = is_numeric($default) ? $default : "'{$default}'";
            $line .= "->default({$defaultValue})";
        }
        
        if ($comment) {
            $escapedComment = addslashes($comment);
            $line .= "->comment('{$escapedComment}')";
        }
        
        $line .= ";\n";
        
        return $line;
    }
    
    private function mapTypeToLaravel($name, $type, $default)
    {
        $base = $type['base'];
        $params = $type['params'];
        
        // Foreign key pattern detection
        if (preg_match('/_id$/', $name) && $base === 'bigint') {
            if ($name === 'company_id' || $name === 'user_id' || $name === 'seller_id') {
                return "foreignId('{$name}')";
            }
            return "unsignedBigInteger('{$name}')";
        }
        
        if ($name === 'email') {
            return "string('{$name}')";
        }
        
        if ($name === 'remember_token') {
            return "rememberToken()";
        }
        
        // Standard types
        switch ($base) {
            case 'bigint':
                if (stripos($params, 'UNSIGNED') !== false || strpos($params, 'UNSIGNED') !== false) {
                    return "unsignedBigInteger('{$name}')";
                }
                return "bigInteger('{$name}')";
                
            case 'int':
            case 'integer':
                $size = '';
                if (preg_match('/\((\d+)\)/', $params, $match)) {
                    $size = $match[1];
                }
                if (stripos($type['base'] . $params, 'UNSIGNED') !== false) {
                    return "unsignedInteger('{$name}')";
                }
                return "integer('{$name}')";
                
            case 'mediumint':
                return "mediumInteger('{$name}')";
                
            case 'tinyint':
                // Check if it's boolean (tinyint(1))
                if (strpos($params, '(1)') !== false) {
                    return "boolean('{$name}')";
                }
                return "tinyInteger('{$name}')";
                
            case 'varchar':
                preg_match('/\((\d+)\)/', $params, $match);
                $length = isset($match[1]) ? $match[1] : 255;
                return "string('{$name}', {$length})";
                
            case 'char':
                preg_match('/\((\d+)\)/', $params, $match);
                $length = isset($match[1]) ? $match[1] : 36;
                return "char('{$name}', {$length})";
                
            case 'text':
                return "text('{$name}')";
                
            case 'longtext':
                return "longText('{$name}')";
                
            case 'enum':
                // Parse enum values
                if (preg_match('/enum\((.*?)\)/i', $params, $match)) {
                    $values = $match[1];
                    return "enum('{$name}', [{$values}])";
                }
                return "string('{$name}')";
                
            case 'decimal':
            case 'double':
            case 'float':
                preg_match('/\((\d+),\s*(\d+)\)/', $params, $match);
                if (isset($match[1]) && isset($match[2])) {
                    return "{$base}('{$name}', {$match[1]}, {$match[2]})";
                }
                return "{$base}('{$name}')";
                
            case 'date':
                return "date('{$name}')";
                
            case 'datetime':
                return "dateTime('{$name}')";
                
            case 'timestamp':
                return "timestamp('{$name}')";
                
            case 'year':
                return "year('{$name}')";
                
            case 'json':
                return "json('{$name}')";
                
            default:
                return "string('{$name}')";
        }
    }
    
    private function isForeignKeyIndex($indexName)
    {
        // Foreign key index pattern
        return preg_match('/_foreign$/', $indexName);
    }
    
    private function generateIndexDefinition($index)
    {
        $columns = implode("', '", $index['columns']);
        
        if ($index['unique']) {
            if (count($index['columns']) === 1) {
                return "            \$table->unique('{$columns}');\n";
            } else {
                return "            \$table->unique(['{$columns}']);\n";
            }
        } else {
            if (count($index['columns']) === 1) {
                return "            \$table->index('{$columns}');\n";
            } else {
                return "            \$table->index(['{$columns}']);\n";
            }
        }
    }
    
    private function generateForeignKeyDefinition($fk)
    {
        $line = "            \$table->foreign('{$fk['column']}')";
        $line .= "->references('{$fk['references_column']}')";
        $line .= "->on('{$fk['references_table']}')";
        
        if ($fk['on_delete'] !== 'restrict') {
            $line .= "->onDelete('{$fk['on_delete']}')";
        }
        
        if ($fk['on_update'] !== 'restrict' && $fk['on_update'] !== '') {
            $line .= "->onUpdate('{$fk['on_update']}')";
        }
        
        $line .= ";\n";
        
        return $line;
    }
    
    // Special handling for circular dependencies
    private function needsAlterMigration($tableName)
    {
        return $tableName === 'sellers' && $this->counter > 1005;
    }
}

// Script'i çalıştır
$generator = new MigrationGenerator();
$generator->run();

echo "\n💡 Sonraki adım:\n";
echo "   php artisan migrate:fresh\n";
echo "   php artisan db:seed\n";

