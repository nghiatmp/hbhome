export const IS_LINKED = {
  TRUE: 1,
  FALSE: 0,
};
export const TYPE_ACTIVITY_LOG = [
    {key : '0', value : 'All' },
    {key : '1', value : 'Project' },
    {key : '2', value : 'Phase' },
    {key : '3', value : 'Resource' },
    {key : '4', value : 'Member' },
];
export const  SYSTEM_ROLE = [
    {key : '0', value : 'Inactive' },
    {key : '3', value : 'Member' },
    {key : '7', value : 'Leader' },
    {key : '15', value : 'Admin' },
];
export const USER_ROLE_STRING = {
    Member: 3,
    Leader: 7,
    Admin: 15,
    Inactive:0
};
export const TYPE_ACTIVITY_LOG_DEFAULT = '0';

export const PHASE_STATUS = [
    {key : '0', value : 'Close' },
    {key : '1', value : 'Open' },
];
export const PROJECT_STATUS = [
    {key : '0', value : 'Close' },
    {key : '1', value : 'Open' },
];
export const PROJECT_RANK = [
    {key : '1', value : 'A' },
    {key : '2', value : 'B' },
    {key : '3', value : 'C' },
    {key : '4', value : 'D' },
    {key : '5', value : 'E' },
];
export const PROJECT_CONTRACT = [
    {key : '1', value : 'FIX_PRICE' },
    {key : '2', value : 'LABOR' },
    {key : '3', value : 'BODY_SHOPPING' },
];
export const MEMBER_STATUS = [
    {key : '1', value : 'Active' },
    {key : '0', value : 'InActive' },
];
export const PROJECT_ROLE = [
    {key : '1', value : 'DEVELOPER' },
    {key : '2', value : 'TESTER' },
    {key : '3', value : 'BA' },
    {key : '4', value : 'COMTOR' },
    {key : '5', value : 'ACCOUNT' },
    {key : '6', value : 'QA' },
    {key : '7', value : 'OTHERS' },
    {key : '15', value : 'ADMIN' },
];
export const PROJECT_ROLE_STRING = {
    DEVELOPER : 1,
    TESTER : 2,
    BA : 3,
    COMTOR : 4,
    ACCOUNT : 5,
    QA : 6,
    OTHERS : 7,
    ADMIN : 15
};
