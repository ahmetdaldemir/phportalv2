// MongoDB Initialization Script for Laravel Activity Logs
// This script runs when the MongoDB container starts for the first time

// Switch to the activity logs database
db = db.getSiblingDB('phportal_logs');

// Create collections for different types of logs
db.createCollection('activity_logs');
db.createCollection('system_logs');
db.createCollection('error_logs');
db.createCollection('access_logs');
db.createCollection('performance_logs');

// Create indexes for better performance
db.activity_logs.createIndex({ "created_at": -1 });
db.activity_logs.createIndex({ "user_id": 1 });
db.activity_logs.createIndex({ "company_id": 1 });
db.activity_logs.createIndex({ "event": 1 });
db.activity_logs.createIndex({ "subject_type": 1, "subject_id": 1 });

db.system_logs.createIndex({ "timestamp": -1 });
db.system_logs.createIndex({ "level": 1 });
db.system_logs.createIndex({ "component": 1 });

db.error_logs.createIndex({ "timestamp": -1 });
db.error_logs.createIndex({ "level": 1 });
db.error_logs.createIndex({ "file": 1 });

db.access_logs.createIndex({ "timestamp": -1 });
db.access_logs.createIndex({ "ip_address": 1 });
db.access_logs.createIndex({ "user_agent": 1 });

db.performance_logs.createIndex({ "timestamp": -1 });
db.performance_logs.createIndex({ "duration": -1 });
db.performance_logs.createIndex({ "endpoint": 1 });

// Create a user for the application (optional, for authentication)
db.createUser({
    user: "phportal_user",
    pwd: "phportal_password",
    roles: [
        {
            role: "readWrite",
            db: "phportal_logs"
        }
    ]
});

// Insert initial configuration document
db.config.insertOne({
    _id: "app_config",
    name: "PHP Portal Activity Logs",
    version: "1.0.0",
    created_at: new Date(),
    settings: {
        log_retention_days: 90,
        max_log_size_mb: 1000,
        compression_enabled: true,
        backup_enabled: true
    }
});

// Create capped collections for real-time logs (optional)
db.createCollection('realtime_logs', {
    capped: true,
    size: 100000000, // 100MB
    max: 10000 // Max 10,000 documents
});

db.realtime_logs.createIndex({ "timestamp": -1 });

print("MongoDB initialization completed successfully!");
print("Database: phportal_logs");
print("Collections created: activity_logs, system_logs, error_logs, access_logs, performance_logs, realtime_logs");
print("User created: phportal_user");
