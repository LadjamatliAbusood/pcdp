import { useConfirm } from "primevue/useconfirm";

export default function useConfirmAlert() {
    const confirm = useConfirm();

    const confirmAlert = ({
        target = null,
        message = "Are you sure?",
        icon = "pi pi-exclamation-triangle",
        header = null,
        acceptLabel = "OK",
        rejectLabel = "Cancel",
        acceptClass = "",
        rejectClass = "",
        acceptProps = {},
        rejectProps = {},
        onAccept = () => {},
        onReject = () => {}
    }) => {

        confirm.require({
            target,
            message,
            icon,
            header,

            acceptProps: {
                label: acceptLabel,
                class: acceptClass,
                severity: 'info',
                ...acceptProps
            },

            rejectProps: {
                label: rejectLabel,
                class: rejectClass,
                severity: 'danger',
                ...rejectProps
            },

            accept: onAccept,
            reject: onReject
        });
    };

    return { confirmAlert };
}
