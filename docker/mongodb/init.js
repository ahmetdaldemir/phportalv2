// Create database and collections
db = db.getSiblingDB('phportal');

// Create collections
db.createCollection('logs');
db.createCollection('analytics');
db.createCollection('cache');

// Create indexes for better performance
db.logs.createIndex({ "timestamp": -1 });
db.logs.createIndex({ "level": 1 });
db.analytics.createIndex({ "created_at": -1 });
db.cache.createIndex({ "key": 1 }, { unique: true });

// Insert initial data
db.logs.insertOne({
    timestamp: new Date(),
    level: "info",
    message: "MongoDB initialized successfully",
    context: "system"
});

print("MongoDB initialization completed!");
