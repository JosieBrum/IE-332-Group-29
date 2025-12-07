-- QUERY 2: Transactions EXITING
-- Shows all shipments leaving from companies based on filtering

SELECT 
    s.ShipmentID,
    src.CompanyName AS Company,
    s.Quantity,
    s.ActualDate AS Date,
    p.ProductName AS Product
FROM Shipping s
JOIN InventoryTransaction t ON t.TransactionID = s.TransactionID
JOIN Product p ON p.ProductID = s.ProductID
JOIN Company src ON src.CompanyID = s.SourceCompanyID
JOIN Location lsrc ON lsrc.LocationID = src.LocationID
WHERE t.Type = 'Shipping'
  AND s.ActualDate >= '2019-01-01'
  AND s.ActualDate <= '2025-12-31'
  AND ('' = '' OR src.CompanyName = '')
  AND ('' = '' OR lsrc.City = '')
  AND ('' = '' OR lsrc.CountryName = '')
  AND ('Europe' = '' OR lsrc.ContinentName = 'Europe')
  AND ('' = '' OR src.TierLevel = '')
ORDER BY s.ActualDate, s.ShipmentID;