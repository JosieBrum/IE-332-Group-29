-- Disruption Frequency per company over a date range
-- & Network level 
SELECT 
    c.CompanyName,
    COUNT(*) AS num_disruptions, --Delete Line if Number of Disruptions not needed
    (TIMESTAMPDIFF(MONTH, '2019-01-01', '2025-12-31') + 1) AS observation_period_months, --Delete Line if not needed
    (COUNT(*) * 1.0  --Ongshu! You might need to change commas to make queries work after deleting the lines above.
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

-- Filters (accounting for all possible user inputs)
    AND ('' = '' OR c.CompanyName   = '')         
    AND ('' = '' OR l.CountryName   = '')         
    AND ('' = '' OR l.ContinentName = '')          
    AND ('1' = '' OR c.TierLevel     = '1')          
GROUP BY 
    c.CompanyID,
    c.CompanyName
ORDER BY 
    df_per_month DESC;