// Create database
db = db.getSiblingDB(dbName);
print("Database '"+ dbName +"' created.");

// Create collections
db.createCollection("buildings");
print("Collection 'buildings' created.");

db.createCollection("categories");
print("Collection 'categories' created.");

db.createCollection("companies");
print("Collection 'companies' created.");

// Setup indexes
db.buildings.createIndex( { "loc" : "2dsphere" } );
db.companies.createIndex( { "building._id" : 1 } );
db.companies.createIndex( { "building.loc" : "2dsphere" } );
db.companies.createIndex( { "categories" : 1 } );
print("All indexes are created.");
