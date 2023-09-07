export const aspectRatioStore = new Ext.data.Store({
    fields: ['key', 'value'],
    data: [
        {
            key: '16:9',
            value: '16:9',
        },
        {
            key: '4:3',
            value: '4:3',
        },
        {
            key: '3:2',
            value: '3:2',
        },
        {
            key: '16:10',
            value: '16:10',
        },
        {
            key: '5:4',
            value: '5:4',
        },
        {
            key: '1:1',
            value: '1:1',
        },
        {
            key: '21:9',
            value: '21:9',
        },
    ]
});

export const aspectRatioStoreDefault = '1:1';
