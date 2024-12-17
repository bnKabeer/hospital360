<?php
require_once './db/db.php'; // Include your database connection

class Migration
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function runMigrations()
    {
        // Get the list of migrations that have already been applied
        $appliedMigrations = $this->getAppliedMigrations();

        // Get all migration files
        $migrationFiles = glob('migrations/sql/*.sql');

        foreach ($migrationFiles as $file) {
            $filename = basename($file);

            // Check if the migration has already been applied
            if (in_array($filename, $appliedMigrations)) {
                continue;
            }

            // Apply the migration
            echo "Applying migration: $filename\n";
            $this->applyMigration($file, $filename);
        }

        echo "All migrations are applied.\n";
    }

    private function getAppliedMigrations()
    {
        $stmt = $this->pdo->query('SELECT migration_name FROM migrations');
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    private function applyMigration($file, $filename)
    {
        $sql = file_get_contents($file);

        // Start a transaction to ensure that the migration is atomic
        $this->pdo->beginTransaction();
        try {
            $this->pdo->exec($sql);  // Execute the SQL commands from the migration file
            $this->recordMigration($filename); // Record the migration in the database
            $this->pdo->commit();  // Commit the transaction
        } catch (Exception $e) {
            $this->pdo->rollBack();  // Rollback if something goes wrong
            echo "Failed to apply migration: $filename\n";
            echo "Error: " . $e->getMessage() . "\n";
        }
    }

    private function recordMigration($filename)
    {
        $stmt = $this->pdo->prepare('INSERT INTO migrations (migration_name) VALUES (:migration_name)');
        $stmt->execute(['migration_name' => $filename]);
    }
}

// Run migrations
$migration = new Migration($pdo);
$migration->runMigrations();
?>
