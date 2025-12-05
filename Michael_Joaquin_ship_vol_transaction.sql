SELECT
    COUNT(*) AS NumberOfShipments,
    SUM(s.Quantity) AS TotalShipmentVolume,
    SUM(s.Quantity) / COUNT(*) AS AverageShipmentVolume
FROM Shipping s
JOIN InventoryTransaction t ON t.TransactionID = s.TransactionID
JOIN Distributor d ON d.CompanyID = s.DistributorID
JOIN Company c ON c.CompanyID = d.CompanyID
WHERE t.Type = 'Shipping'
  AND s.ActualDate BETWEEN '2019-01-01' AND '2025-12-31'
  AND c.CompanyName = 'Garcia-Trujillo'