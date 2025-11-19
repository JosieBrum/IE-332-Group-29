-- Disruption Severity Distribution
SELECT
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
WHERE e.EventDate BETWEEN '2019-01-01' AND '2025-12-31'
 --  Filters 
  AND ('Haynes-Long' = '' OR c.CompanyName = 'Haynes-Long')
  AND ('' = '' OR l.CountryName   = '')
  AND ('' = '' OR l.ContinentName = '')
  AND ('' = '' OR c.TierLevel     = '');