-- Disruption Exposoure
SELECT
    c.CompanyName,
    --COUNT(ic.EventID) AS TotalDisruptions,
    --COUNT(CASE WHEN ic.ImpactLevel = 'High' THEN 1 END) AS HighImpactEvents,
    (COUNT(ic.EventID) + (2 * COUNT(CASE WHEN ic.ImpactLevel = 'High' THEN 1 END))) AS DisruptionExposure
FROM ImpactsCompany ic
JOIN DisruptionEvent de ON ic.EventID = de.EventID
JOIN Company c ON ic.AffectedCompanyID = c.CompanyID
WHERE
    de.EventDate BETWEEN '2019-01-01' AND '2025-12-31'
    AND c.CompanyName = 'Smith Inc'
GROUP BY
    c.CompanyName;
