CREATE VIEW dommer_view AS
SELECT
    d.*,
    kilder.navn as kilde_navn
FROM dommer AS d
JOIN dommer_kilder AS kilder
    ON d.kilde_id = kilder.id
