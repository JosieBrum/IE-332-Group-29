-- Products handled by a Distributor/s with Filters
SELECT DISTINCT
    p.ProductID,
    p.ProductName
FROM Shipping AS s
JOIN Product AS p  
    ON s.ProductID = p.ProductID
JOIN Company AS dist  
    ON s.DistributorID = dist.CompanyID
JOIN Location AS l  
    ON dist.LocationID = l.LocationID
WHERE
    s.ActualDate BETWEEN '2019-01-01' AND '2025-12-31'
    AND ('Esparza, Lopez and Kelly' = '' OR dist.CompanyName = 'Esparza, Lopez and Kelly')
    AND ('' = '' OR l.CountryName = '')
    AND ('' = '' OR l.ContinentName = '')
ORDER BY
    p.ProductID ASC, p.ProductName ASC;
