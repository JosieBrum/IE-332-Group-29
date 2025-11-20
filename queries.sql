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

    
"TOTAL DOWNTIME QUERIES:"
"Query 1 Filteration by Company" 
SELECT 
    DATE_FORMAT(e.EventDate, '%Y-%m') AS month,
    c.CompanyName,
    COUNT(DISTINCT e.EventID) AS disruption_count,
    SUM(DATEDIFF(e.EventRecoveryDate, e.EventDate)) AS total_downtime_days
FROM 
    DisruptionEvent AS e
    JOIN ImpactsCompany AS ic ON e.EventID = ic.EventID
    JOIN Company AS c ON ic.AffectedCompanyID = c.CompanyID
WHERE 
    c.CompanyName = ?
    AND e.EventDate BETWEEN ? AND ?
GROUP BY 
    DATE_FORMAT(e.EventDate, '%Y-%m'),
    c.CompanyName
ORDER BY 
    month;

"Query 2 Filteration by Tier Level"
SELECT 
    DATE_FORMAT(e.EventDate, '%Y-%m') AS month,
    c.TierLevel,
    COUNT(DISTINCT e.EventID) AS disruption_count,
    SUM(DATEDIFF(e.EventRecoveryDate, e.EventDate)) AS total_downtime_days
FROM 
    DisruptionEvent AS e
    JOIN ImpactsCompany AS ic ON e.EventID = ic.EventID
    JOIN Company AS c ON ic.AffectedCompanyID = c.CompanyID
WHERE 
    c.TierLevel = ?
    AND e.EventDate BETWEEN ? AND ?
GROUP BY 
    DATE_FORMAT(e.EventDate, '%Y-%m'),
    c.TierLevel
ORDER BY 
    month;

"Query 3 Filteration by Country"
SELECT 
    DATE_FORMAT(e.EventDate, '%Y-%m') AS month,
    l.CountryName,
    COUNT(DISTINCT e.EventID) AS disruption_count,
    SUM(DATEDIFF(e.EventRecoveryDate, e.EventDate)) AS total_downtime_days
FROM 
    DisruptionEvent AS e
    JOIN ImpactsCompany AS ic ON e.EventID = ic.EventID
    JOIN Company AS c ON ic.AffectedCompanyID = c.CompanyID
    JOIN Location AS l ON c.LocationID = l.LocationID
WHERE 
    l.CountryName = ?
    AND e.EventDate BETWEEN ? AND ?
GROUP BY 
    DATE_FORMAT(e.EventDate, '%Y-%m'),
    l.CountryName
ORDER BY 
    month;
"Query 4 Filteration by Continent"
SELECT 
    DATE_FORMAT(e.EventDate, '%Y-%m') AS month,
    l.ContinentName,
    COUNT(DISTINCT e.EventID) AS disruption_count,
    SUM(DATEDIFF(e.EventRecoveryDate, e.EventDate)) AS total_downtime_days
FROM 
    DisruptionEvent AS e
    JOIN ImpactsCompany AS ic ON e.EventID = ic.EventID
    JOIN Company AS c ON ic.AffectedCompanyID = c.CompanyID
    JOIN Location AS l ON c.LocationID = l.LocationID
WHERE 
    l.ContinentName = ?
    AND e.EventDate BETWEEN ? AND ?
GROUP BY 
    DATE_FORMAT(e.EventDate, '%Y-%m'),
    l.ContinentName
ORDER BY 
    month;

"Query 5 Filteration by Tier and Country"
SELECT 
    DATE_FORMAT(e.EventDate, '%Y-%m') AS month,
    c.TierLevel,
    l.CountryName,
    COUNT(DISTINCT e.EventID) AS disruption_count,
    SUM(DATEDIFF(e.EventRecoveryDate, e.EventDate)) AS total_downtime_days
FROM 
    DisruptionEvent AS e
    JOIN ImpactsCompany AS ic ON e.EventID = ic.EventID
    JOIN Company AS c ON ic.AffectedCompanyID = c.CompanyID
    JOIN Location AS l ON c.LocationID = l.LocationID
WHERE 
    c.TierLevel = ?
    AND l.CountryName = ?
    AND e.EventDate BETWEEN ? AND ?
GROUP BY 
    DATE_FORMAT(e.EventDate, '%Y-%m'),
    c.TierLevel,
    l.CountryName
ORDER BY 
    month;

"Query 6 Filteration by Tier and Continent"
SELECT 
    DATE_FORMAT(e.EventDate, '%Y-%m') AS month,
    c.TierLevel,
    l.ContinentName,
    COUNT(DISTINCT e.EventID) AS disruption_count,
    SUM(DATEDIFF(e.EventRecoveryDate, e.EventDate)) AS total_downtime_days
FROM 
    DisruptionEvent AS e
    JOIN ImpactsCompany AS ic ON e.EventID = ic.EventID
    JOIN Company AS c ON ic.AffectedCompanyID = c.CompanyID
    JOIN Location AS l ON c.LocationID = l.LocationID
WHERE 
    c.TierLevel = ?
    AND l.ContinentName = ?
    AND e.EventDate BETWEEN ? AND ?
GROUP BY 
    DATE_FORMAT(e.EventDate, '%Y-%m'),
    c.TierLevel,
    l.ContinentName
ORDER BY 
    month;

"All Queries are to be filtered by 2 or more factors, a variable filteration + date"



