SELECT
    l.ContinentName,
    COUNT(DISTINCT CASE WHEN ic.ImpactLevel = 'High' THEN de.EventID END) AS HighImpactEvents,
    total.TotalEvents,
    (COUNT(DISTINCT CASE WHEN ic.ImpactLevel = 'High' THEN de.EventID END) * 1.0 / total.TotalEvents) AS PercentHigh
FROM DisruptionEvent de
JOIN ImpactsCompany ic ON ic.EventID = de.EventID
JOIN Company c = ON ic.AffectedCompanyID = c.CompanyID
JOIN Location l ON c.LocationID = l.LocationID
CROSS JOIN (
    SELECT COUNT(DISTINCT e2.EventID) AS TotalEvents
    FROM DisruptionEvent e2
    WHERE
      e2.EventDate <= '2025-12-31' AND e2.EventRecoveryDate >= '2019-01-01'
      AND EXISTS (
        SELECT 1
        FROM ImpactsCompany ic2
        JOIN Company  c2 ON c2.CompanyID  = ic2.AffectedCompanyID
        JOIN Location l2 ON l2.LocationID = c2.LocationID
        WHERE ic2.EventID = e2.EventID
          AND ('' = '' OR c2.TierLevel     = '')
          AND ('' = '' OR l2.CountryName   = '')
          AND ('' = '' OR l2.ContinentName = '')
      )
) AS total
WHERE
    de.EventDate <= '2025-12-31' AND de.EventRecoveryDate >= '2019-01-01'
    AND ('' = '' OR c.TierLevel     = '')
    AND ('' = '' OR l.CountryName   = '')
    AND ('' = '' OR l.ContinentName = '')
GROUP BY
    l.ContinentName,
    total.TotalEvents
ORDER BY
    HighImpactEvents DESC;
