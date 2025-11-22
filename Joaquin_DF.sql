-- Disruption Frequency per company over a date range

//Company 
SELECT 
    c.CompanyName,
    COUNT(*) AS num_disruptions,
    (TIMESTAMPDIFF(MONTH, '2019-01-01', '2025-12-31') + 1) AS observation_period_months,
    (COUNT(*) * 1.0 
        / NULLIF(TIMESTAMPDIFF(MONTH, '2019-01-01', '2025-12-31') + 1, 0)
    ) AS df_per_month
FROM 
    DisruptionEvent AS e
    JOIN ImpactsCompany AS ic 
        ON e.EventID = ic.EventID
    JOIN Company AS c 
        ON ic.AffectedCompanyID = c.CompanyID
    JOIN Location AS l
        ON c.LocationID = l.LocationID
WHERE 
    c.CompanyName = 'Haynes-Long'
    AND e.EventDate BETWEEN '2019-01-01' AND '2025-12-31'
GROUP BY 
    c.CompanyID,
    c.CompanyName
ORDER BY 
    df_per_month DESC;

//Tier 
SELECT 
    c.CompanyName,
    COUNT(*) AS num_disruptions,
    (TIMESTAMPDIFF(MONTH, '2019-01-01', '2025-12-31') + 1) AS observation_period_months,
    (COUNT(*) * 1.0 
        / NULLIF(TIMESTAMPDIFF(MONTH, '2019-01-01', '2025-12-31') + 1, 0)
    ) AS df_per_month
FROM 
    DisruptionEvent AS e
    JOIN ImpactsCompany AS ic 
        ON e.EventID = ic.EventID
    JOIN Company AS c 
        ON ic.AffectedCompanyID = c.CompanyID
    JOIN Location AS l
        ON c.LocationID = l.LocationID
WHERE 
    c.TierLevel = '1'
    AND e.EventDate BETWEEN '2019-01-01' AND '2025-12-31'
GROUP BY 
    c.CompanyID,
    c.CompanyName
ORDER BY 
    df_per_month DESC


-- Network level 
SELECT 
    COUNT(DISTINCT e.EventID) AS num_disruptions,
    (TIMESTAMPDIFF(MONTH, '2019-01-01', '2025-12-31') + 1) AS observation_period_months,
    (COUNT(DISTINCT e.EventID) * 1.0 
        / NULLIF(TIMESTAMPDIFF(MONTH, '2019-01-01', '2025-12-31') + 1, 0)
    ) AS df_per_month
FROM 
    DisruptionEvent AS e
    JOIN ImpactsCompany AS ic 
        ON e.EventID = ic.EventID
    JOIN Company AS c 
        ON ic.AffectedCompanyID = c.CompanyID
    JOIN Location AS l
        ON c.LocationID = l.LocationID
WHERE 
    e.EventDate BETWEEN '2019-01-01' AND '2025-12-31'
    --  Filters 
    AND ('1' = '' OR c.TierLevel = '1')
    AND ('' = '' OR l.CountryName = '')

    AND ('' = '' OR l.ContinentName = '');
