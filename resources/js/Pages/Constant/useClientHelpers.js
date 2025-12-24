export function useClientHelpers() {

    const getSexLabel = (value) => {
        return value === 1 ? 'Male' : (value === 2 ? 'Female' : 'N/A');
    };

    const formatDate = (dateValue) => {
        if (!dateValue) return 'N/A';
        const date = new Date(dateValue);
        return date.toLocaleDateString('en-US', { 
            year: 'numeric', 
            month: 'long', 
            day: 'numeric' 
        });
    };

    const shortformatDate = (dateValue) => {
        if (!dateValue) return 'N/A';
        const date = new Date(dateValue);
        return date.toLocaleDateString('en-US', { 
            year: 'numeric', 
            month: 'short', 
            day: 'numeric' 
        });
    };
    const getAge = (birthdate) => {
        if (!birthdate) return 'N/A';

        const b = new Date(birthdate);
        const today = new Date();

        let age = today.getFullYear() - b.getFullYear();
        const m = today.getMonth() - b.getMonth();

        if (m < 0 || (m === 0 && today.getDate() < b.getDate())) {
            age--;
        }

        return age;
    };


     const getEducationOptions = (value) => {
        return value === 1
            ? "College"
            : value === 2
            ? "Senior High"
            : value === 3
            ? "Junior High"
            : value === 4
            ? "Elementary"
            : value === 5
            ? "None"
            : "N/A";
    
    };

        const getDurationInMalaysia = (value) => {
        return value === 1
            ? "Days"
            : value === 2
            ? "Weeks"
            : value === 3
            ? "Months"
            : value === 4
            ? "Years"
            : "N/A";
    
    };

        const ClientPlan = (value) => {
        return value === 'remain_in_the_ph'
            ? "Remain in the Philippines"
            : value === 'return_to_malaysia'
            ? "Return to Malaysia"
            : "N/A";
    
    };
    return {
        getSexLabel,
        formatDate,
        getAge,
        shortformatDate,
        getEducationOptions,
        getDurationInMalaysia,
        ClientPlan
    };
}
