-- QUERY 1: Transactions ENTERING
-- Shows all shipments arriving at companies based on filtering

SELECT 
    s.ShipmentID,
    dst.CompanyName AS Company,
    s.Quantity,
    s.ActualDate AS Date,
    p.ProductName AS Product
FROM Shipping s
JOIN InventoryTransaction t ON t.TransactionID = s.TransactionID
JOIN Product p ON p.ProductID = s.ProductID
JOIN Company dst ON dst.CompanyID = s.DestinationCompanyID
JOIN Location ldst ON ldst.LocationID = dst.LocationID
WHERE t.Type = 'Shipping'
  AND s.ActualDate >= '2019-01-01'
  AND s.ActualDate <= '2025-12-31'
  AND ('' = '' OR dst.CompanyName = '')
  AND ('' = '' OR ldst.City = '')
  AND ('' = '' OR ldst.CountryName = '')
  AND ('Europe' = '' OR ldst.ContinentName = 'Europe')
  AND ('' = '' OR dst.TierLevel = '')
ORDER BY s.ActualDate, s.ShipmentID;