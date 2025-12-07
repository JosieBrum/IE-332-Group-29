-- Top Distributors by Shipment Volume with filters

SELECT
    c.CompanyName AS Distributor,
    SUM(s.Quantity) AS TotalShipmentVolume
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

  AND ('' = '' OR c.TierLevel = '')
  AND ('' = '' OR l.CountryName = '')
  AND ('' = '' OR l.ContinentName = '')
GROUP BY 
    c.CompanyID, 
    c.CompanyName
ORDER BY 
    TotalShipmentVolume DESC;