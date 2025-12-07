-- Which shipments are currently out/status distribution
-- Delivered SHIPMENTS
SELECT 
    COUNT(s.ShipmentID) AS DeliveredShipments
FROM Shipping s
JOIN Company dist ON s.DistributorID = dist.CompanyID
WHERE s.PromisedDate BETWEEN '2019-01-01' AND '2025-12-31'  
  AND s.ActualDate IS NOT NULL 
  AND s.ActualDate <= '2025-12-31'  
AND ('Davis PLC' = '' OR dist.CompanyName = 'Davis PLC')
