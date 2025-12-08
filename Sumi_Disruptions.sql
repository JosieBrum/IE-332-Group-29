-- Disruption events by company
SELECT
    e.EventId,
    dc.CategoryName
FROM DisruptionEvent AS e
JOIN DisruptionCategory AS dc
  ON dc.CategoryID = e.CategoryID
JOIN ImpactsCompany AS ic
  ON ic.EventId = e.EventId
JOIN Company AS c
  ON c.CompanyId = ic.AffectedCompanyID
WHERE
    e.EventDate <= '2025-12-31' AND e.EventRecoveryDate >= '2019-01-01'
    AND ('Smith Inc' = '' OR c.CompanyName = 'Smith Inc')
ORDER BY e.EventDate ASC, e.EventId;

-- Disruption events by event type
SELECT DISTINCT
    c.CompanyName
FROM DisruptionEvent AS e
JOIN DisruptionCategory AS dc
  ON dc.CategoryID = e.CategoryID
JOIN ImpactsCompany AS ic
  ON ic.EventId = e.EventId
JOIN Company AS c
  ON c.CompanyId = ic.AffectedCompanyID
WHERE
    e.EventDate <= '2025-12-31' AND e.EventRecoveryDate >= '2019-01-01'
    AND ('' = '' OR dc.CategoryName = '')
ORDER BY c.CompanyName;
