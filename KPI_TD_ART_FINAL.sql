"On time delievry KPI"
SELECT 
    ROUND(
        100.0 * SUM(CASE 
            WHEN s.ActualDate <= s.PromisedDate 
            THEN 1 
            ELSE 0 
        END) / COUNT(*),
        2
    ) AS on_time_delivery_rate_percent
FROM 
    Shipping AS s
    JOIN Company AS c ON s.SourceCompanyID = c.CompanyID
WHERE 
    c.CompanyName = '".$companyName."'
    AND s.ActualDate BETWEEN '".$startDate."' AND '".$endDate."'

"TD Query"
SELECT 
    SUM(DATEDIFF(e.EventRecoveryDate, e.EventDate)) AS total_downtime_days
FROM 
    DisruptionEvent AS e
    JOIN ImpactsCompany AS ic 
        ON e.EventID = ic.EventID
    JOIN Company AS c 
        ON ic.AffectedCompanyID = c.CompanyID
    LEFT JOIN Location AS l 
        ON c.LocationID = l.LocationID

WHERE e.EventDate BETWEEN '2019-01-01' AND '2025-12-31'
  AND ('' = '' OR c.CompanyName   = '')
  AND ('' = '' OR l.CountryName   = '')
  AND ('' = '' OR l.ContinentName = '')
  AND ('' = '' OR c.TierLevel    = '')


