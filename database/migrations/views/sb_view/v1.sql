CREATE MATERIALIZED VIEW sb_view AS
SELECT
    sb_publication.*

    , STRING_AGG(DISTINCT sb_creator.name, '; ') AS creators
    , STRING_AGG(DISTINCT sb_category.name, '; ') AS categories

FROM sb_publication

-- Authors
LEFT JOIN sb_creator_publication AS creator_pivot
    ON sb_publication.id = creator_pivot.publication_id
    LEFT JOIN sb_creator
        ON sb_creator.id = creator_pivot.creator_id

-- Categories
LEFT JOIN sb_category_publication AS category_pivot
    ON sb_publication.id = category_pivot.publication_id
LEFT JOIN sb_category
    ON sb_category.id = category_pivot.category_id

GROUP BY sb_publication.id;

CREATE UNIQUE INDEX sb_view_id ON sb_view (id);

