-- Disruption Severity Distribution
SELECT
    c.CompanyName,
    SUM(CASE WHEN ic.ImpactLevel = 'Low'    THEN 1 ELSE 0 END) AS low_count,
    SUM(CASE WHEN ic.ImpactLevel = 'Medium' THEN 1 ELSE 0 END) AS medium_count,
    SUM(CASE WHEN ic.ImpactLevel = 'High'   THEN 1 ELSE 0 END) AS high_count
FROM DisruptionEvent AS e
JOIN ImpactsCompany AS ic 
    ON e.EventID = ic.EventID
JOIN Company AS c 
    ON ic.AffectedCompanyID = c.CompanyID
JOIN Location AS l
    ON c.LocationID = l.LocationID
WHERE e.EventDate <= '2025-12-31' and e.EventRecoveryDate >= '2024-01-01'

-- Filters

  AND ('' = '' OR c.CompanyName   = '')
  AND ('China' = '' OR l.CountryName   = 'China')
  AND ('' = '' OR l.ContinentName = '')
  AND ('' = '' OR c.TierLevel    = '')
GROUP BY c.CompanyID, c.CompanyName

ORDER BY c.CompanyName;
