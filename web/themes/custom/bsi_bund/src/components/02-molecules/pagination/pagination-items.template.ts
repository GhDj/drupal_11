// Single page item.
interface PaginationItemPage {
  type: "page";
  page: number;
}

// Visual gap item.
interface PaginationItemEllipsis {
  type: "ellipsis";
}

// Renderable pagination item.
type PaginationItem = PaginationItemPage | PaginationItemEllipsis;

const createPageItem = (page: number): PaginationItemPage => ({
  type: "page",
  page
});

const createEllipsisItem = (): PaginationItemEllipsis => ({
  type: "ellipsis"
});

export function createPaginationItems(
  paginationCurrentPage: number,
  paginationTotalPages: number
): PaginationItem[] {
  // Show all pages for small totals.
  if (paginationTotalPages <= 5) {
    return Array.from({ length: paginationTotalPages }, (_, index) =>
      createPageItem(index + 1)
    );
  }

  // Start range
  if (paginationCurrentPage < 3) {
    return [
      createPageItem(1),
      createPageItem(2),
      createPageItem(3),
      createEllipsisItem(),
      createPageItem(paginationTotalPages)
    ];
  }

  if (paginationCurrentPage === 3) {
    return [
      createPageItem(1),
      createPageItem(2),
      createPageItem(3),
      createPageItem(4),
      createEllipsisItem(),
      createPageItem(paginationTotalPages)
    ];
  }

  if (paginationCurrentPage === 4) {
    return [
      createPageItem(1),
      createPageItem(2),
      createPageItem(3),
      createPageItem(4),
      createPageItem(5),
      createEllipsisItem(),
      createPageItem(paginationTotalPages)
    ];
  }

  // End range
  if (paginationCurrentPage === paginationTotalPages - 3) {
    return [
      createPageItem(1),
      createEllipsisItem(),
      createPageItem(paginationTotalPages - 4),
      createPageItem(paginationTotalPages - 3),
      createPageItem(paginationTotalPages - 2),
      createPageItem(paginationTotalPages - 1),
      createPageItem(paginationTotalPages)
    ];
  }

  if (paginationCurrentPage === paginationTotalPages - 2) {
    return [
      createPageItem(1),
      createEllipsisItem(),
      createPageItem(paginationTotalPages - 3),
      createPageItem(paginationTotalPages - 2),
      createPageItem(paginationTotalPages - 1),
      createPageItem(paginationTotalPages)
    ];
  }

  if (paginationCurrentPage > paginationTotalPages - 3) {
    return [
      createPageItem(1),
      createEllipsisItem(),
      createPageItem(paginationTotalPages - 2),
      createPageItem(paginationTotalPages - 1),
      createPageItem(paginationTotalPages)
    ];
  }

  // Default/Middle range
  return [
    createPageItem(1),
    createEllipsisItem(),
    createPageItem(paginationCurrentPage - 1),
    createPageItem(paginationCurrentPage),
    createPageItem(paginationCurrentPage + 1),
    createEllipsisItem(),
    createPageItem(paginationTotalPages)
  ];
}
