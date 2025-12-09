-- Query 1: Check if LocationID  Already Exists
SELECT EXISTS(
    SELECT 1 
    FROM Location 
    WHERE City = '' 
      AND CountryName = ''
      AND ContinentName = '';
) AS LocationIDExists;

-- If LocationID does not exist, insert new location
INSERT INTO Location (City, CountryName, ContinentName)
VALUES ('', '', '');

--Then User inputs Company Name

-- Query 2: Check if Company Name Already Exists
SELECT EXISTS(
    SELECT 1 
    FROM Company 
    WHERE CompanyName = ''
) AS CompanyExists;

-- Query 3: Insert New Company
INSERT INTO Company (CompanyName, TierLevel, Type, LocationID)
VALUES ('', '', '', '');

-- Query 4A: Insert into Manufacturer table
INSERT INTO Manufacturer (CompanyID, FactoryCapacity)
VALUES (LAST_INSERT_ID(), '');

-- Query 4B: Insert into Distributor table
INSERT INTO Distributor (CompanyID)
VALUES (LAST_INSERT_ID());

-- Query 4C: Insert into Retailer table
INSERT INTO Retailer (CompanyID)
VALUES (LAST_INSERT_ID());