SELECT 
    c.CompanyName,  
    (COUNT(CASE WHEN ic.ImpactLevel = 'High' THEN 1 END) * 100 / NULLIF (COUNT(ic.EventID), 0))  
    AS HDR 
FROM ImpactsCompany ic 
JOIN DisruptionEvent de 
    ON ic.EventID = de.EventID 
JOIN Company c 
    ON ic.AffectedCompanyID = c.CompanyID
WHERE
    c.CompanyName = 'Smith Inc'
    AND (de.EventDate BETWEEN '2021-06-12' AND '2021-06-21')   
GROUP BY 
    c.CompanyName
