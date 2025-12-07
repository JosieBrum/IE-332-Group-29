-- QUERY 3: Transaction ADJUSTMENTS
-- Shows all inventory adjustments for companies based on filtering

SELECT 
    a.AdjustmentID,
    a.AdjustmentDate AS Date,
    c.CompanyName AS Company,
    p.ProductName AS Product,
    a.QuantityChange AS Quantity,
    a.Reason
FROM InventoryAdjustment a
JOIN InventoryTransaction t ON t.TransactionID = a.TransactionID
JOIN Company c ON c.CompanyID = a.CompanyID
JOIN Location l ON l.LocationID = c.LocationID
JOIN Product p ON p.ProductID = a.ProductID
WHERE t.Type = 'Adjustment'
  AND a.AdjustmentDate >= '2019-01-01'
  AND a.AdjustmentDate <= '2025-12-31'
  AND ('' = '' OR c.CompanyName = '')
  AND ('' = '' OR l.City = '')
  AND ('' = '' OR l.CountryName = '')
  AND ('Europe' = '' OR l.ContinentName = 'Europe')
  AND ('' = '' OR c.TierLevel = '')
ORDER BY a.AdjustmentDate, a.AdjustmentID;