import ConfigStorage from "./ConfigStorage";
import ServiceEnum from "./ServiceEnum";
import FeatureEnum from "./FeatureEnum";

class FeatureHelper {

    isFeatureEnabled(feature) {
        const adapter = ConfigStorage.get('adapter', null);
        const featureService = adapter ? adapter[feature] : null;

        switch (featureService) {
            case ServiceEnum.STABLE_DIFFUSION:
                switch (feature) {
                    case FeatureEnum.TXT2IMG:
                    case FeatureEnum.UPSCALE:
                    case FeatureEnum.INPAINT:
                    case FeatureEnum.INPAINT_BACKGROUND:
                    case FeatureEnum.IMAGE_VARIATIONS:
                        return true;
                    default:
                        return false;
                }
            case ServiceEnum.DREAM_STUDIO:
                switch (feature) {
                    case FeatureEnum.TXT2IMG:
                    case FeatureEnum.INPAINT:
                    case FeatureEnum.UPSCALE:
                    case FeatureEnum.IMAGE_VARIATIONS:
                        return true;
                    default:
                        return false;
                }
            case ServiceEnum.OPEN_AI:
                switch (feature) {
                    case FeatureEnum.TXT2IMG:
                    case FeatureEnum.INPAINT:
                    case FeatureEnum.INPAINT_BACKGROUND:
                    case FeatureEnum.IMAGE_VARIATIONS:
                        return true;
                    default:
                        return false;
                }
            case ServiceEnum.CLIP_DROP:
                switch (feature) {
                    case FeatureEnum.TXT2IMG:
                    case FeatureEnum.UPSCALE:
                    case FeatureEnum.INPAINT_BACKGROUND:
                    case FeatureEnum.IMAGE_VARIATIONS:
                        return true;
                    default:
                        return false;
                }
            default:
                return false;
        }
    }

    isSeedingSupported(feature) {
        const adapter = ConfigStorage.get('adapter', null);
        const featureService = adapter ? adapter[feature] : null;

        return featureService === ServiceEnum.STABLE_DIFFUSION || featureService === ServiceEnum.DREAM_STUDIO;
    }

    isAspectRatioSupported(feature) {
        const adapter = ConfigStorage.get('adapter', null);
        const featureService = adapter ? adapter[feature] : null;

        return feature === FeatureEnum.TXT2IMG && featureService !== ServiceEnum.CLIP_DROP;
    }
}

export default new FeatureHelper();
