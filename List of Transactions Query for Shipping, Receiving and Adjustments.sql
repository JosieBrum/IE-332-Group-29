-- SHIPPING transactions involving Haynes-Long between 2019-01-01 and 2025-12-31
SELECT
    t.TransactionID,
    'Shipping' AS TransactionType,
    s.ActualDate AS TransactionDate,
    p.ProductName,
    src.CompanyName AS SourceCompanyName,
    s.Quantity
FROM InventoryTransaction AS t
JOIN Shipping AS s
    ON t.TransactionID = s.TransactionID
JOIN Company AS src
    ON s.SourceCompanyID = src.CompanyID
JOIN Product AS p
    ON s.ProductID = p.ProductID
WHERE t.Type = 'Shipping'
  AND s.ActualDate BETWEEN '2019-01-01' AND '2025-12-31'
  AND src.CompanyName = 'Haynes-Long'
ORDER BY s.ActualDate, t.TransactionID;




-- RECEIVING transactions involving Haynes-Long between 2019-01-01 and 2025-12-31
SELECT
    t.TransactionID,
    'Receiving' AS TransactionType,
    r.ReceivedDate AS TransactionDate,
    p.ProductName,
    rc.CompanyName AS DestinationCompanyName,
    r.QuantityReceived AS Quantity
FROM InventoryTransaction AS t
JOIN Receiving AS r
    ON t.TransactionID = r.TransactionID
JOIN Shipping AS s
    ON r.ShipmentID = s.ShipmentID
JOIN Product AS p
    ON s.ProductID = p.ProductID
JOIN Company AS src
    ON s.SourceCompanyID = src.CompanyID
JOIN Company AS rc
    ON r.ReceiverCompanyID = rc.CompanyID
WHERE t.Type = 'Receiving'
  AND r.ReceivedDate BETWEEN '2019-01-01' AND '2025-12-31'
  AND rc.CompanyName = 'Haynes-Long'
ORDER BY r.ReceivedDate, t.TransactionID;





-- ADJUSTMENT transactions for Haynes-Long between 2019-01-01 and 2025-12-31
SELECT 
    t.TransactionID,
    'Adjustment' AS TransactionType,
    a.AdjustmentDate AS TransactionDate,
    p.ProductName,
    c.CompanyName AS SourceCompanyName,
    a.QuantityChange AS Quantity,
    a.Reason
FROM InventoryTransaction AS t
JOIN InventoryAdjustment AS a
    ON t.TransactionID = a.TransactionID
JOIN Company AS c
    ON a.CompanyID = c.CompanyID
JOIN Product AS p
    ON a.ProductID = p.ProductID
WHERE t.Type = 'Adjustment'
  AND a.AdjustmentDate BETWEEN '2019-01-01' AND '2025-12-31'
  AND c.CompanyName = 'Haynes-Long'
ORDER BY a.AdjustmentDate, t.TransactionID;



