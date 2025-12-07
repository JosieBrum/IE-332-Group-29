-- Distributors sorted by average delay in days
SELECT
    c.CompanyName AS Distributor,
    AVG(DATEDIFF(s.ActualDate, s.PromisedDate)) AS AvgDelayDays
FROM Shipping s
JOIN InventoryTransaction t 
    ON t.TransactionID = s.TransactionID
JOIN Distributor d 
    ON d.CompanyID = s.DistributorID
JOIN Company c 
    ON c.CompanyID = d.CompanyID
JOIN Location l 
    ON l.LocationID = c.LocationID
WHERE t.Type = 'Shipping'
AND s.ActualDate BETWEEN '2019-01-01' AND '2025-12-31'

GROUP BY 
    c.CompanyID, 
    c.CompanyName
ORDER BY 
    AvgDelayDays DESC;
