-- Disruption Frequency per company over a date range
-- & Network level 
SELECT 
    c.CompanyName,
    COUNT(*) AS num_disruptions, 
    (TIMESTAMPDIFF(MONTH, '2024-01-01', '2025-12-31')) AS observation_period_months, 
    (COUNT(*) * 1.0 
        / NULLIF(TIMESTAMPDIFF(MONTH, '2024-01-01', '2025-12-31'), 0)
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
    e.EventDate <= '2025-12-31' and e.EventRecoveryDate >= '2024-01-01'
    AND ('Haynes-Long' = '' OR c.CompanyName   = 'Haynes-Long')         
    AND ('' = '' OR l.CountryName   = '')         
    AND ('' = '' OR l.ContinentName = '')          
    AND ('' = '' OR c.TierLevel     = '')          
GROUP BY 
    c.CompanyID,
    c.CompanyName
ORDER BY 

    df_per_month DESC
