import { useToast } from "primevue/usetoast";

export default function useNotify() {
    const toast = useToast();

    function success(message, summary = "Success") {
        toast.add({
            severity: "success",
            summary,
            detail: message,
            life: 3000,
        });
    }

    function error(message, summary = "Error") {
        toast.add({
            severity: "error",
            summary,
            detail: message,
            life: 3000,
        });
    }

    function info(message, summary = "Info") {
        toast.add({
            severity: "info",
            summary,
            detail: message,
            life: 3000,
        });
    }

    return { success, error, info };
}
