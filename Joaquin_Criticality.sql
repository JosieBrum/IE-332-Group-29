-- Most Critical Companies (sorted)

SELECT
    c.CompanyName,
    COALESCE(downstream.NumDownstream, 0) * COALESCE(impacts.HighImpactCount, 0) 
        AS Criticality
FROM Company c
JOIN Location l ON l.LocationID = c.LocationID
-- (Subquery) Count the downstream companies for criticality calculation
LEFT JOIN (
    SELECT 
        UpstreamCompanyID,
        COUNT(DISTINCT DownstreamCompanyID) 
            AS NumDownstream
    FROM DependsOn
    GROUP BY UpstreamCompanyID
) AS downstream 
    ON downstream.UpstreamCompanyID = c.CompanyID
-- (Subquery) Counting the # of high impact disruptions for criticality calculation
LEFT JOIN (
    SELECT 
        AffectedCompanyID,
        SUM(CASE WHEN ImpactLevel = 'High' THEN 1 ELSE 0 END) 
            AS HighImpactCount
    FROM ImpactsCompany
    GROUP BY AffectedCompanyID
) AS impacts 
    ON impacts.AffectedCompanyID = c.CompanyID
WHERE 1 = 1

  AND ('' = '' OR l.CountryName = '')
  AND ('' = '' OR l.ContinentName = '')
  AND ('' = '' OR c.TierLevel = '')
ORDER BY 
    Criticality DESC;