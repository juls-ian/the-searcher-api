# Removed code for testing
```php
<?php

require_once 'vendor/autoload.php';

use Algolia\AlgoliaSearch\Api\SearchClient;



$appId = 'BNKPDB6GZ6';
$apiKey = 'df6d9c19e8fac19115c013b66e59b34a';

try {
    $client = SearchClient::create($appId, $apiKey);
    $indices = $client->listIndices();
    echo "✅ Connection successful!\n";
    echo "Available indices: " . json_encode($indices, JSON_PRETTY_PRINT) . "\n";
} catch (Exception $e) {
    echo "❌ Connection failed: " . $e->getMessage() . "\n";
    echo "Error type: " . get_class($e) . "\n";
}
```