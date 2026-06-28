export interface EnumOption {
    value: string;
    label: string;
    icon?: string;
    color?: string;
    emoji?: string;
}

export interface CommunityBrief {
    name: string;
    municipality: string | null;
    state: string | null;
}

export interface TopNeed {
    name: string;
    priority: string;
}

export interface BuildingSummary {
    id: number;
    name: string;
    slug: string;
    type: EnumOption;
    status: EnumOption;
    mode: EnumOption;
    structuralStatus: EnumOption;
    community: CommunityBrief | null;
    address: string | null;
    lat: number | null;
    lng: number | null;
    openNeedsCount: number;
    topNeeds: TopNeed[];
    confidence: string | null;
    lastReportedAt: string | null;
}

export interface NeedCategory {
    name: string;
    icon: string | null;
    color: string | null;
}

export interface NeedCommitment {
    id: number;
    name: string;
    status: EnumOption;
    allowedTransitions: EnumOption[];
    at: string | null;
}

export interface Need {
    id: number;
    name: string;
    category: NeedCategory | null;
    quantity: number | null;
    unit: string | null;
    priority: EnumOption;
    status: EnumOption;
    isOpen: boolean;
    notes: string | null;
    createdBy: string | null;
    claimedBy: string | null;
    claimedAt: string | null;
    createdAt: string | null;
    lastReportedAt: string | null;
    commitmentsCount?: number;
    statusCounts?: NeedStatusCount[];
    commitments?: NeedCommitment[];
}

export interface NeedStatusCount {
    value: string;
    label: string;
    count: number;
}

export interface NeedCategoryCount {
    id: number;
    name: string;
    icon: string | null;
    count: number;
}

export interface NeedsSummary {
    lastHour: number;
    open: number;
    byStatus: NeedStatusCount[];
    topCategories: NeedCategoryCount[];
}

export interface Building {
    id: number;
    name: string;
    slug: string;
    type: EnumOption;
    status: EnumOption;
    mode: EnumOption;
    structuralStatus: EnumOption;
    community: CommunityBrief | null;
    address: string | null;
    lat: number | null;
    lng: number | null;
    peopleEvacuated: number | null;
    residents: number | null;
    contactName: string | null;
    contactPhone: string | null;
    externalPersonsUrl: string | null;
    notes: string | null;
    notesUpdatedAt: string | null;
    sourceUrl: string | null;
    confidence: string | null;
    isLocked: boolean;
    statusIsManual: boolean;
    version: number;
    createdAt: string | null;
    lastReportedAt: string | null;
    lastReportedBy: string | null;
    needs: Need[];
}

export interface Supply {
    id: number;
    name: string;
    unit: string | null;
}

export interface SupplyCategory {
    id: number;
    name: string;
    icon: string | null;
    color: string | null;
    supplies: Supply[];
}

export interface PaginatorLink {
    url: string | null;
    label: string;
    active: boolean;
}

export interface Paginator<T> {
    data: T[];
    current_page: number;
    last_page: number;
    from: number | null;
    to: number | null;
    total: number;
    prev_page_url: string | null;
    next_page_url: string | null;
    links: PaginatorLink[];
}

export interface BuildingStats {
    total: number;
    criticos: number;
    rescate: number;
    sectores: number;
    necesidadesAbiertas: number;
    necesidadesCriticas: number;
    hospitales: number;
}

export interface BuildingHistoryChange {
    field: string;
    old: string | number | null;
    new: string | number | null;
}

export interface BuildingHistoryEntry {
    id: number;
    event: string;
    user: string;
    at: string | null;
    changes: BuildingHistoryChange[];
}
