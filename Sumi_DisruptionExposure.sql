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
    e.EventDate <= '2025-12-31' and e.EventRecoveryDate >= '2024-01-01'
    AND c.CompanyName = 'Smith Inc'
GROUP BY
    c.CompanyName;
