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

"THIS IS THE CORRECT ON TIME DELIVERY KPI, IF YOU MAKE ME MODIFY IT IM GONNA KILL SOMEBODY"
