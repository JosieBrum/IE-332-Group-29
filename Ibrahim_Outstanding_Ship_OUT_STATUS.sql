-- Which shipments are currently out/status distribution
-- Outstanding SHIPMENTS
SELECT 
    COUNT(s.ShipmentID) AS OutstandingShipments
FROM Shipping s
JOIN Company dist ON s.DistributorID = dist.CompanyID
WHERE s.PromisedDate BETWEEN '2025-08-27' AND '2025-08-30'  
  AND (s.ActualDate IS NULL OR s.ActualDate > '2025-08-30')  
AND ('Davis PLC' = '' OR dist.CompanyName = 'Davis PLC')
